<?php

namespace QuantumTecnology\ServiceBasicsExtension\Traits;

use Illuminate\Support\Facades\App;

trait FilterSortTrait
{
    protected ?string $sortBy = null;
    protected string $sort    = 'asc';

    public function addSortFilter(?string $sortBy = null, string $sort = 'asc'): self
    {
        if (!$this->runningInConsole) {
            $this->setSortBy($sortBy ?? request('sort_by'));
            $this->setSort(request('sort', $sort));
        }

        match ($this->getSortBy()) {
            'random' => $this->defaultQuery()->inRandomOrder(),
            default  => $this->defaultQuery()->orderby(
                $this->getSortBy(),
                $this->getSort(),
            ),
        };

        return $this;
    }

    public function getSortBy(): ?string
    {
        return $this->sortBy ?? $this->getModel()->getTable().'.'.$this->getModel()->getKeyName();
    }

    public function setSortBy(?string $sortBy): self
    {
        if (App::runningInConsole()) {
            $this->runningInConsole = true;
        }

        $this->sortBy = $sortBy;

        return $this;
    }

    public function getSort(): ?string
    {
        return $this->sort;
    }

    public function setSort(?string $sort): self
    {
        if (App::runningInConsole()) {
            $this->runningInConsole = true;
        }

        $this->sort = $sort;

        return $this;
    }
}
