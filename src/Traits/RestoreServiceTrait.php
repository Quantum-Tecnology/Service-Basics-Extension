<?php

declare(strict_types = 1);

namespace QuantumTecnology\ServiceBasicsExtension\Traits;

trait RestoreServiceTrait
{
    public function restore(string | int $id): bool
    {
        $query = $this->defaultQuery();

        $model = $query->onlyTrashed()->findOrfail($id);

        $this->setModel($model);

        $this->restoring();

        return $this->restored($model->restore());
    }

    protected function restoring(): void
    {
        //
    }

    protected function restored(bool $deleted): bool
    {
        return $deleted;
    }
}
