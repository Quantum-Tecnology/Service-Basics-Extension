<?php

declare(strict_types = 1);

namespace QuantumTecnology\ServiceBasicsExtension\Traits;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

trait StoreServiceTrait
{
    use FilesTrait;

    public function store(): Model
    {
        $this->storing();

        $this->setData($this->existsData ? $this->data : data());

        $model = $this->getModel();
        $attributes = $this->data->only($model->getFillable());
        $relations = $this->data->except($model->getFillable());

        $model->fill($attributes->toArray());
        $this->setModel($model);

        $transaction = DB::transaction(function () use ($relations) {
            $this->getModel()->save();

            collect($relations->toArray())->each(function ($value, $indice) {
                if ($value instanceof Collection) {
                    $value = $value->toArray();
                }

                if (is_array($value) && method_exists($this->getModel(), $indice)) {
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
