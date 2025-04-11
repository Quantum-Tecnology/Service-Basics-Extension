<?php

namespace QuantumTecnology\ServiceBasicsExtension\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

trait StoreServiceTrait
{
    use FilesTrait;

    public function store(): Model
    {
        $this->setData($this->existsData ? $this->data : data());
        $model = $this->getModel();
        $model->fill($this->data->toArray());
        $this->setModel($model);

        $transaction = DB::transaction(function () {
            collect($this->data)->each(function ($value, $indice) {
                if (is_array($value)) {
                    $this->getModel()->$indice()->sync($value, $this->sync);
                }
            });

            $this->getModel()->save();

            if (in_array(FilesTrait::class, class_uses($this->getModel()), true)) {
                $this->createFiles();
            }

            return $this->getModel()->refresh();
        });

        return $transaction;
    }
}
