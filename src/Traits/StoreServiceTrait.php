<?php

declare(strict_types=1);

namespace QuantumTecnology\ServiceBasicsExtension\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

trait StoreServiceTrait
{
    use FilesTrait;

    public function store(): Model
    {
        $this->storing();

        $this->setData($this->existsData ? $this->data : data());

        $model = $this->getModel();
        $model->fill($this->data->toArray());
        $this->setModel($model);

        $transaction = DB::transaction(function () {
            $this->getModel()->save();

            collect($this->data->toArray())->each(function ($value, $indice) {
                if (is_array($value)) {
                    $this->getModel()->$indice()->sync($value, $this->sync);
                }
            });

            $this->createFiles();
            $this->stored();

            $id = $this->getModel()->getKeyName();
            return $this->show($this->getModel()->{$id});
        });

        return $transaction;
    }

    protected function storing(): void
    {
    }

    protected function stored(): Model
    {
        return $this->getModel();
    }
}
