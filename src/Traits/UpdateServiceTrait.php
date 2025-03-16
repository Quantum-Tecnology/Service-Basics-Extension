<?php

namespace QuantumTecnology\ServiceBasicsExtension\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

trait UpdateServiceTrait
{
    public function update(int $id): Model
    {
        $data  = !$this->existsData ? request()->data() : $this->data;
        $model = $this->show($id);

        $transaction = DB::transaction(function () use ($data, $model) {
            $model->update($data->toArray());

            foreach ($data as $indice => $value) {
                if (is_array($value)) {
                    $model->$indice()->sync($value, $this->sync);
                }
            }

            return $model->refresh();
        });

        return $transaction;
    }
}
