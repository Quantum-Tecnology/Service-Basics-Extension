<?php

namespace QuantumTecnology\ServiceBasicsExtension\Traits;

trait RestoreServiceTrait
{
    public function restore(int $id): bool
    {
        $query = $this->defaultQuery();

        $model = $query->onlyTrashed()->findOrfail($id);

        return $model->restore();
    }
}
