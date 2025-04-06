<?php

namespace QuantumTecnology\ServiceBasicsExtension\Traits;

use QuantumTecnology\PerPageTrait\PerPageTrait;
use QuantumTecnology\ValidateTrait\Data;

trait IndexServiceTrait
{
    use FilterSearchTrait;
    use FilterSortTrait;
    use FilterTrashTrait;
    use FilterIncludeTrait;
    use FilterScopesTrait;
    use PerPageTrait;

    protected bool $runningInConsole = false;

    public function index(): Data
    {
        $this->defaultQuery();
        $this->addIncludeFilter();
        $this->addTrashFilter();
        $this->addSearchFilter();
        $this->addSortFilter();
        $this->addScopesFilter();

        $indexed = $this->result();

        $indexed->data->transform(function ($item) {
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
