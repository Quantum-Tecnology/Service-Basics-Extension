<?php

declare(strict_types = 1);

namespace QuantumTecnology\ServiceBasicsExtension\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

trait UpdateServiceTrait
{
    use FilesTrait;

    public function update(int $id): Model
    {
        $this->updating();

        $this->setData($this->existsData ? $this->data : data());
        $model = $this->getModel()->findOrfail($id);
        $model->fill($this->data->toArray());
        $this->setModel($model);

        $transaction = DB::transaction(function () {
            collect($this->data)->each(function ($value, $indice) {
                if (is_array($value)) {
                    $this->getModel()->$indice()->sync($value, $this->sync);
                }
            });

            $this->getModel()->save();

            $this->destroyFiles();
            $this->updateFiles();
            $this->createFiles();

            return $this->updated()->refresh();
        });

        return $transaction;
    }

    protected function updating(): void
    {
        //
    }

    protected function updated(): Model
    {
        return $this->getModel();
    }
}
