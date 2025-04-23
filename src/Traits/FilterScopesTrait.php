<?php

namespace QuantumTecnology\ServiceBasicsExtension\Traits;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;

trait FilterScopesTrait
{
    protected ?array $scopes = [];

    public function addScopesFilter(?array $scopes = []): self
    {
        if (blank($this->getScopes())) {
            $this->setScopes($scopes);
        }

        if (!$this->runningInConsole) {
            $this->setScopes(request('filter', []));
        }

        $this->getScopes()
            ->each(function ($scope, $key) {
                $nameFilter = str("by_{$key}")->camel()->toString();
                $nameScoped = str("scope_by_{$key}")->camel()->toString();
                if (!method_exists($this->getModel(), $nameScoped)) {
                    return;
                }

                $dataFilter = collect(explode('|', $filter ?? ''))
                    ->filter(fn ($item) => filled($item))
                    ->toArray();

                $this->defaultQuery()->$nameFilter(array_values($dataFilter));
            });

        return $this;
    }

    public function setScopes(?array $scopes): self
    {
        if (App::runningInConsole()) {
            $this->runningInConsole = true;
        }

        $this->scopes = $scopes;

        return $this;
    }

    public function getScopes(): Collection
    {
        return collect($this->scopes);
    }
}
