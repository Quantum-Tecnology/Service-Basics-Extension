<?php

namespace QuantumTecnology\ServiceBasicsExtension\Traits;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;

trait FilterSearchTrait
{
    protected ?string $search = '';

    protected array $searchableColumns = [];

    protected array $searchableRelations = [];

    public function addSearchFilter(?string $search = ''): self
    {
        if (blank($this->getSearch())) {
            $this->setSearch($search);
        }

        if (!$this->runningInConsole) {
            $this->setSearch(request('search', $this->getSearch()));
        }

        abort_if(
            $this->isSearchable(),
            Response::HTTP_UNPROCESSABLE_ENTITY,
            __('Parameter search not enabled this route.'),
        );

        $this->defaultQuery()->where(function ($subQuery) {
            $searchString = str_replace(' ', '%', trim($this->getSearch()));

            $subQuery->where(function ($query) use ($searchString) {
                foreach ($this->searchableColumns as $column) {
                    $query->orWhere($column, 'LIKE', "%{$searchString}%");
                }
            });

            foreach ($this->searchableRelations as $relation => $columns) {
                $subQuery->orWhereHas($relation, function ($relationQuery) use ($columns, $searchString) {
                    $relationQuery->where(function ($query) use ($columns, $searchString) {
                        foreach ($columns as $relationColumn) {
                            $query->orWhere($relationColumn, 'LIKE', "%{$searchString}%");
                        }
                    });
                });
            }
        });

        return $this;
    }

    private function isSearchable(): bool
    {
        return !blank($this->getSearch()) && !(count($this->searchableColumns) > 0 || count($this->searchableRelations) > 0);
    }

    public function setSearch(string $search): self
    {
        if (App::runningInConsole()) {
            $this->runningInConsole = true;
        }

        $this->search = $search;

        return $this;
    }

    public function getSearch(): string
    {
        return $this->search;
    }
}
