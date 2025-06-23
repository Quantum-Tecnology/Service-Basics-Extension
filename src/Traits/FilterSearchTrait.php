<?php

declare(strict_types = 1);

namespace QuantumTecnology\ServiceBasicsExtension\Traits;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

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
            $this->setSearch((string) request(config('servicebase.parameters_default.search'), $this->getSearch()));
        }

        abort_if(
            $this->isSearchable(),
            Response::HTTP_UNPROCESSABLE_ENTITY,
            __('Parameter search not enabled this route.'),
        );

        $this->defaultQuery()->where(function ($subQuery) {
            $searchString = mb_trim($this->getSearch());

            $subQuery->when($searchString, fn ($query) => $query->where(function ($query) use ($searchString) {
                foreach ($this->searchableColumns as $column) {
                    if (Str::startsWith($searchString, config('servicebase.sensitivity_character')) && Str::endsWith($searchString, config('servicebase.sensitivity_character'))) {
                        $searchString = Str::replace(config('servicebase.sensitivity_character'), '', $searchString);
                        $query->orWhere($column, 'LIKE', "%{$searchString}%");
                    } else {
                        $query->orWhereRaw("REPLACE(LOWER($column), ' ', '-') LIKE ?", ['%' . Str::slug($searchString) . '%']);
                    }
                }

                foreach ($this->searchableRelations as $relation => $columns) {
                    $query->orWhereHas($relation, function ($relationQuery) use ($columns, $searchString) {
                        $relationQuery->where(function ($query) use ($columns, $searchString) {
                            foreach ($columns as $relationColumn) {
                                if (Str::startsWith($searchString, config('servicebase.sensitivity_character')) && Str::endsWith($searchString, config('servicebase.sensitivity_character'))) {
                                    $searchString = Str::replace(config('servicebase.sensitivity_character'), '', $searchString);
                                    $query->orWhere($relationColumn, 'LIKE', "%{$searchString}%");
                                } else {
                                    $query->orWhereRaw("REPLACE(LOWER($relationColumn), ' ', '-') LIKE ?", ['%' . Str::slug($searchString) . '%']);
                                }
                            }
                        });
                    });
                }
            }));
        });

        return $this;
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

    private function isSearchable(): bool
    {
        return !blank($this->getSearch()) && !(count($this->searchableColumns) > 0 || count($this->searchableRelations) > 0);
    }
}
