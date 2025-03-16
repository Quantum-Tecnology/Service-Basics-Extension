<?php

namespace QuantumTecnology\ServiceBasicsExtension\Traits;

use Illuminate\Database\Eloquent\Model;

trait ShowServiceTrait
{
    public function show(int $id): Model
    {
        $this->include();

        $showed = $this->defaultQuery()->findOrfail($id);

        foreach ($showed->getRelations() as $index => $relation) {
            if (is_null($relation)) {
                $showed->$index();
            }
        }

        return $showed;
    }
}
