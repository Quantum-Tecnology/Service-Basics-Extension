<?php

namespace QuantumTecnology\ServiceBasicsExtension\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

trait StoreServiceTrait
{
    public function store(): Model
    {
        $data = !$this->existsData ? request()->data() : $this->data;

        $transaction = DB::transaction(function () use ($data) {
            $callback = $this->defaultModel->create($data->toArray());

            foreach ($data as $indice => $value) {
                if (is_array($value)) {
                    $indice = Str::Camel($indice);
                    $callback->$indice()->sync($value);
                }
            }

            return $callback->refresh();
        });

        return $transaction;
    }
}
