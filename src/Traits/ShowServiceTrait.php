<?php

declare(strict_types = 1);

namespace QuantumTecnology\ServiceBasicsExtension\Traits;

use Illuminate\Database\Eloquent\Model;

trait ShowServiceTrait
{
    use FilterIncludeTrait;

    protected bool $runningInConsole = false;

    public function show(string | int $id): Model
    {
        $this->addIncludeFilter();

        $showed = $this->defaultQuery()->findOrfail($id);

        $this->setModel($showed);

        foreach ($showed->getRelations() as $index => $relation) {
            if (is_null($relation)) {
                $showed->$index();
            }
        }

        return $showed;
    }
}
