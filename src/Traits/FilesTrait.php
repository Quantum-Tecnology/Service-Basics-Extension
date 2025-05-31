<?php

declare(strict_types = 1);

namespace QuantumTecnology\ServiceBasicsExtension\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

trait FilesTrait
{
    public const DELETE                 = 'delete';
    protected Collection | array $files = [];

    protected function checkFile(
        string $nameInData = 'files',
        string $name = 'file',
        ?string $meta = null,
        string $path = 'files',
        string $rule = 'private',
    ): self {
        if (data()->has($nameInData)) {
            $this->files[$nameInData] = collect(data()->$nameInData)
                ->transform(function ($file, $index) use ($name, $rule, $meta, $path) {
                    $file['order'] = $index;
                    $file['main']  = false;
                    $file['name']  = $name;
                    $file['meta']  = $meta;
                    $file['path']  = $path;
                    $file['rule']  = $rule;

                    return $file;
                })
                ->values();

            $firstNonDeletedKey = $this->files[$nameInData]
                ->search(function ($file) {
                    return ($file['action'] ?? null) !== self::DELETE;
                });

            if (false !== $firstNonDeletedKey) {
                $this->files[$nameInData] = $this->files[$nameInData]->map(function ($file, $key) use ($firstNonDeletedKey) {
                    if ($key === $firstNonDeletedKey) {
                        $file['main'] = true;
                    }

                    return $file;
                });
            }

            unset(data()->$nameInData);
            $this->setData(data());
        }

        return $this;
    }

    protected function destroyFiles(): void
    {
        $id = [];
        collect($this->files)->each(function ($fileType) use (&$id) {
            collect($fileType)
                ->where('action', self::DELETE)
                ->each(function ($file) use (&$id) {
                    $id[] = $file['id'];
                });
        });

        if ($id) {
            $this
                ->getModel()
                ->archives()
                ->whereIn('id', $id)
                ->get()
                ->each(function ($archive) {
                    Storage::delete($archive->key);
                    $archive->forceDelete();
                });
        }
    }

    protected function updateFiles(): void
    {
        collect($this->files)->each(function ($fileType) {
            collect($fileType)->each(function ($file) {
                if (isset($file['id']) && ($file['action'] ?? null) !== self::DELETE) {
                    $archive = $this->getModel()
                        ->archives()
                        ->find($file['id']);

                    $archive->main  = $file['main'];
                    $archive->order = $file['order'];

                    if (isset($file['active'])) {
                        $archive->active = $file['active'];
                    }

                    $key = $this->generateKeyUpload($file, $archive);

                    if (
                        ($file['data'] ?? null)
                        && ($file['mime'] ?? null)
                        && Storage::put($key, base64_decode($file['data']), ['ContentType' => $file['mime']])) {
                        Storage::delete($archive->key);
                        $archive->key = $key;
                    }

                    if ($archive->isDirty()) {
                        $archive->save();
                    }

                    return;
                }
            });
        });
    }

    protected function createFiles(): void
    {
        collect($this->files)->each(function ($fileType) {
            collect($fileType)->each(function ($file) {
                if(($file['action'] ?? null) === self::DELETE || ($data['id'] ?? null) !== null) {
                    return;
                }


                $key = $this->generateKeyUpload($file);

                if (Storage::put($key, base64_decode($file['data']), ['ContentType' => $file['mime']])) {
                    Storage::setVisibility($key, $file['rule']);
                    $this
                        ->getModel()
                        ->archives()
                        ->create([
                            'name'   => $file['name'],
                            'main'   => $file['main'],
                            'active' => $file['active'],
                            'order'  => $file['order'],
                            'key'    => $key,
                        ]);
                }
            });
        });
    }

    protected function generateKeyUpload($file, ?Model $model = null): string
    {
        return sprintf(
            '%s/%s/%s/%s/%s_%s',
            config('app.env'),
            $file['meta'] ?? $model?->meta,
            $file['rule'] ?? $model?->rule,
            $file['path'] ?? $model?->path,
            $file['name'] ?? $model?->name,
            uniqid()
        );
    }

}
