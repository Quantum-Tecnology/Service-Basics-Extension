<?php

namespace QuantumTecnology\ServiceBasicsExtension\Traits;

use Illuminate\Database\Eloquent\SoftDeletes;

trait FilterTrashTrait
{
    public function addTrashFilter(
        ?string $trashed = null,
    ): void {
        $existSoftDelete = in_array(SoftDeletes::class, class_uses($this->defaultModel));

        if ($existSoftDelete && 'only' === $trashed) {
            $this->query->onlyTrashed();
        }
    }
}
