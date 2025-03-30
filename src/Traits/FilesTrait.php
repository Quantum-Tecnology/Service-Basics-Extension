<?php

namespace QuantumTecnology\ServiceBasicsExtension\Traits;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

trait FilesTrait
{
    protected Collection|array $files = [];

    protected function checkFile(
        string $nameInData = 'files',
        string $name = 'file',
        ?string $meta = null,
        string $path = 'files',
        string $rule = 'private',
    ): self {
        if (data()->has($nameInData)) {
            $this->files = collect(data()->$nameInData)
                ->transform(function ($file, $index) use ($name, $rule, $meta, $path) {
                    $file['order'] = $index;
                    $file['main']  = 0 === $index;
                    $file['name']  = $name;
                    $file['meta']  = $meta;
                    $file['path']  = $path;
                    $file['rule']  = $rule;

                    return $file;
                });

            unset(data()->$nameInData);

            $this->setData(data());
        }

        return $this;
    }

    protected function destroyFiles(): void
    {
        $query = $this
            ->getModel()
            ->archives();

        $this->files->each(function ($file) use (&$query) {
            $query
                ->when($file['id'] ?? false, function ($query) use ($file) {
                    $query->where(function ($query) use ($file) {
                        $query
                            ->where('name', $file['name'])
                            ->whereNot('id', $file['id']);
                    });
                });
        });

        $query
        ->get()
        ->each(function ($archive) {
            Storage::delete($archive->key);
            $archive->forceDelete();
        });
    }

    protected function updateFiles(): void
    {
        $this->files->each(function ($file) {
            if (isset($file['id'])) {
                $archive = $this->getModel()
                    ->archives()
                    ->find($file['id']);

                $archive->main  = $file['main'];
                $archive->order = $file['order'];

                if (isset($file['active'])) {
                    $archive->active = $file['active'];
                }

                if ($archive->isDirty()) {
                    $archive->save();
                }

                return;
            }
        });
    }

    protected function createFiles(): void
    {
        collect($this->files)->each(function ($file) {
            $key = sprintf(
                '%s/%s/%s/%s/%s_%s',
                config('app.env'),
                $file['meta'],
                $file['rule'],
                $file['path'],
                $file['name'],
                uniqid()
            );

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
    }
}
