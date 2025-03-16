<?php

namespace QuantumTecnology\ServiceBasicsExtension\Traits;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

trait IndexServiceTrait
{
    public function index(
        ?string $search = null,
        ?array $filters = [],
        ?string $sortby = null,
        ?string $sortdir = null,
        ?string $include = null,
        ?string $trashed = null,
    ): LengthAwarePaginator|Collection {
        $this->include($include ?: '');

        $query = $this->defaultQuery();

        $table          = $this->defaultModel->getTable();
        $tableAndColumn = $table.'.'.$this->defaultModel->getKeyName();

        $sortby          = $sortby ?: $tableAndColumn;
        $existSoftDelete = in_array(SoftDeletes::class, class_uses($this->defaultModel));

        if ($existSoftDelete && 'only' === $trashed) {
            $query->onlyTrashed();
        }

        if (!empty($search) && (count($this->searchableColumns) > 0 || count($this->searchableRelations) > 0)) {
            $query->where(function ($subQuery) use ($search) {
                $searchString = str_replace(' ', '%', trim($search));

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
        } elseif (!empty($search) && 0 === count($this->searchableColumns) && 0 === count($this->searchableRelations)) {
            $this->unprocessableEntityException('Parameter search not enabled this route.');
        }

        foreach ($filters as $key => $value) {
            $nameFilter = str("by_{$key}")->camel()->toString();
            $nameScoped = str("scope_by_{$key}")->camel()->toString();
            $dataFilter = collect(explode('|', $filter ?? ''))
                ->filter(fn ($item) => filled($item))
                ->toArray();

            $query->$nameFilter(array_values($dataFilter));
        }

        if ('random' === $sortby) {
            $query->inRandomOrder();
        } else {
            $table          = $this->defaultModel->getTable();
            $tableAndColumn = $table.'.'.$this->defaultModel->getKeyName();

            $query->orderby($sortby, $sortdir ?: 'asc');
        }

        $indexed = $this->result($query);

        $indexed->transform(function ($item) {
            foreach ($item->getRelations() as $index => $relation) {
                if (is_null($relation)) {
                    $item->$index();
                }
            }

            return $item;
        });

        return $indexed;
    }
}
