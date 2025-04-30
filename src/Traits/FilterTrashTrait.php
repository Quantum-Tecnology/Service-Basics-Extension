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
            $this->setTrashed(request(config('servicebase.parameters_default.trash'), $this->getTrashed()));
        }

        if (blank($this->getTrashed())) {
            return;
        }

        abort_if(
            $this->isTrashable(),
            Response::HTTP_UNPROCESSABLE_ENTITY,
            __('Parameter trash not enabled this route.'),
        );

        match ($this->getTrashed()) {
            'with'  => $this->defaultQuery()->withTrashed(),
            'only'  => $this->defaultQuery()->onlyTrashed(),
            default => abort(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                __(sprintf(
                    'Parameter %s is not valid for trash. Valid parameters are: "with", "only".',
                    $this->getTrashed()
                ))
            ),
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
