<?php

namespace QuantumTecnology\ServiceBasicsExtension\Traits;

use Illuminate\Database\Eloquent\Model;

trait ShowServiceTrait
{
    use FilterIncludeTrait;
    
    protected bool $runningInConsole = false;

    public function show(int $id): Model
    {
        $this->addIncludeFilter();

        $showed = $this->defaultQuery()->findOrfail($id);

        foreach ($showed->getRelations() as $index => $relation) {
            if (is_null($relation)) {
                $showed->$index();
            }
        }

        return $showed;
    }
}
