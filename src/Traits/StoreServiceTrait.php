<?php

declare(strict_types = 1);

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
            collect($this->data)->each(function ($value, $indice) {
                if (is_array($value)) {
                    $this->getModel()->$indice()->sync($value, $this->sync);
                }
            });

            $this->getModel()->save();

            $this->createFiles();

            return $this->stored()->refresh();
        });

        return $transaction;
    }

    protected function storing(): void
    {
        //
    }

    protected function stored(): Model
    {
        return $this->getModel();
    }
}
