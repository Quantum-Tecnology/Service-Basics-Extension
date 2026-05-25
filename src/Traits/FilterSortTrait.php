<?php

declare(strict_types=1);

namespace QuantumTecnology\ServiceBasicsExtension\Traits;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Schema;

trait FilterSortTrait
{
    protected ?string $sortBy = null;
    protected string $sort    = 'asc';

    public function addSortFilter(?string $sortBy = null, string $sort = 'asc'): self
    {
        if (!$this->runningInConsole) {
            $this->setSortBy($sortBy ?? request(config('servicebase.parameters_default.sort_by'), $this->sortBy));
            $this->setSort(request(config('servicebase.parameters_default.sort'), $sort));
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
        $model            = $this->defaultQuery()->getModel();
        $defaultSortBy    = $model->getTable().'.'.$model->getKeyName();
        $requestedSortBy  = $this->sortBy;

        if (null === $requestedSortBy || 'random' === $requestedSortBy) {
            return $requestedSortBy ?? $defaultSortBy;
        }

        [$table, $column] = str_contains($requestedSortBy, '.')
            ? explode('.', $requestedSortBy, 2)
            : [$model->getTable(), $requestedSortBy];

        if (!Schema::hasColumn($table, $column)) {
            return $defaultSortBy;
        }

        return $requestedSortBy;
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
