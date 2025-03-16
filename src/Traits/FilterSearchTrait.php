<?php

namespace QuantumTecnology\ServiceBasicsExtension\Traits;

use Illuminate\Http\Response;

trait FilterSearchTrait
{
    public function addSearchFilter(
        ?string $search = null,
    ): void {
        abort_if(
            !empty($search) && (count($this->searchableColumns) > 0 || count($this->searchableRelations) > 0),
            Response::HTTP_UNPROCESSABLE_ENTITY,
            __('Parameter search not enabled this route.'),
        );

        $this->query->where(function ($subQuery) use ($search) {
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
    }
}
