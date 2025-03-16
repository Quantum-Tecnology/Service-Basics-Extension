<?php

namespace QuantumTecnology\ServiceBasicsExtension\Traits;

trait FilterSortTrait
{
    public function addSortFilter(?string $sortBy = null, string $sort = 'asc'): void
    {
        $sortBy = $sortBy ?? request(
            'sortBy',
            $this->getModel()->getTable().'.'.$this->getModel()->getKeyName()
        );

        match ($sortBy) {
            'random' => $this->query->inRandomOrder(),
            default  => $this->query->orderby(
                $sortBy,
                request('sort', $sort ?? 'asc'),
            ),
        };
    }
}
