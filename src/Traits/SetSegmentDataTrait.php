<?php

namespace QuantumTecnology\ServiceBasicsExtension\Traits;

use Illuminate\Database\Eloquent\Collection;

trait SetSegmentDataTrait
{
    public function setSegmentData(
        object $data,
        array $segmentAttributes,
    ): Collection {
        foreach ($segmentAttributes as $attribute) {
            if (isset($data->$attribute)) {
                $segment[$attribute] = $data->$attribute;
                unset($data->$attribute);
            }
        }

        $this->setData($data);

        return new Collection($segment ?? []);
    }
}
