<?php

namespace GustavoSantarosa\ServiceBasicsExtension;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use GustavoSantarosa\ValidateTrait\ValidateTrait;

class BaseService
{
    use ValidateTrait;

    protected ?Model $model;

    public function index(): Collection
    {
        return $this->model::get();
    }

    public function show(int $id): Model
    {
        return $this->model::findOrfail($id);
    }

    public function store(): Model
    {
        return $this->model::create($this->validate(toArray: true));
    }

    public function update(int $id): Model
    {
        $user = $this->show($id);

        $user->update($this->validate());

        return $user->refresh();
    }

    public function destroy(int $id): bool
    {
        $user = $this->show($id);

        return $user->delete();
    }

    public function setModel(string|Model $value): self
    {
        $this->model = $value instanceof Model ? $value : new $value();

        return $this;
    }
}
