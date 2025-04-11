<?php

declare(strict_types = 1);

namespace QuantumTecnology\ServiceBasicsExtension\Traits;

trait DestroyServiceTrait
{
    public function destroy(int $id): bool
    {
        $user = $this->show($id);

        $this->destroying();

        return $this->destroyed($user->delete());
    }

    protected function destroying(): void
    {
        //
    }

    protected function destroyed(bool $deleted): bool
    {
        return $deleted;
    }
}
