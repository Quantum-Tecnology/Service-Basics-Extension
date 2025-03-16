<?php

namespace QuantumTecnology\ServiceBasicsExtension\Traits;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use QuantumTecnology\PerPageTrait\PerPageTrait;

trait IndexServiceTrait
{
    use FilterSearchTrait;
    use FilterSortTrait;
    use FilterTrashTrait;
    use FilterIncludeTrait;
    use FilterScopesTrait;
    use PerPageTrait;

    protected bool $runningInConsole = false;

    public function index(): LengthAwarePaginator|Collection
    {
        $this->defaultQuery();
        $this->addIncludeFilter();
        $this->addTrashFilter();
        $this->addSearchFilter();
        $this->addSortFilter();
        $this->addScopesFilter();

        $indexed = $this->result();

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
