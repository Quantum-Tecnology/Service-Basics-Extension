<?php

namespace QuantumTecnology\ServiceBasicsExtension\Traits;

trait DestroyServiceTrait
{
    public function destroy(int $id): bool
    {
        $user = $this->show($id);

        return $user->delete();
    }
}
