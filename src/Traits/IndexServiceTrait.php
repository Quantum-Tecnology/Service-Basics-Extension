<?php

namespace QuantumTecnology\ServiceBasicsExtension\Traits;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

trait IndexServiceTrait
{
    use FilterSearchTrait;
    use FilterSortTrait;
    use FilterTrashTrait;

    public function index(
        ?string $search = null,
        ?array $filters = [],
        ?string $sortby = null,
        string $sort = 'asc',
        string $includes = '',
        ?string $trashed = null,
    ): LengthAwarePaginator|Collection {
        $this->include($includes);

        $this->defaultQuery();

        $this->addTrashFilter($trashed);
        $this->addSearchFilter($search);
        $this->addSortFilter($sortby, $sort);

        foreach ($filters as $key => $value) {
            $nameFilter = str("by_{$key}")->camel()->toString();
            $nameScoped = str("scope_by_{$key}")->camel()->toString();
            $dataFilter = collect(explode('|', $filter ?? ''))
                ->filter(fn ($item) => filled($item))
                ->toArray();

            $this->query->$nameFilter(array_values($dataFilter));
        }

        $indexed = $this->result($this->query);

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
