<?php

namespace QuantumTecnology\ServiceBasicsExtension\Traits;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;

trait FilterTrashTrait
{
    protected ?string $trashed = '';

    public function addTrashFilter(
        ?string $trashed = null,
    ): void {
        if (blank($this->getTrashed())) {
            $this->setTrashed($trashed);
        }

        if (!$this->runningInConsole) {
            $this->setTrashed(request('filter', false)['trashed'] ?? $this->getTrashed());
        }

        abort_if(
            $this->isTrashable(),
            Response::HTTP_UNPROCESSABLE_ENTITY,
            __('Parameter trashed not enabled this route.'),
        );

        match ($this->getTrashed()) {
            'with'  => $this->defaultQuery()->withTrashed(),
            'only'  => $this->defaultQuery()->onlyTrashed(),
            default => $this->defaultQuery(),
        };
    }

    private function isTrashable(): bool
    {
        return !blank($this->getTrashed()) && !in_array(SoftDeletes::class, class_uses($this->getModel()));
    }

    public function getTrashed(): ?string
    {
        return $this->trashed;
    }

    public function setTrashed(?string $trashed): self
    {
        if (App::runningInConsole()) {
            $this->runningInConsole = true;
        }

        $this->trashed = $trashed;

        return $this;
    }
}
