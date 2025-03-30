<?php

namespace QuantumTecnology\ServiceBasicsExtension\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use QuantumTecnology\ServiceBasicsExtension\Models\Archive;

trait ArchiveModelTrait
{
    /**
     * Archives function.
     */
    public function archives(): MorphMany
    {
        return $this->morphMany(Archive::class, 'archivable');
    }
}
