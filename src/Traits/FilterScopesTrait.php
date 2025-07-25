<?php

declare(strict_types = 1);

namespace QuantumTecnology\ServiceBasicsExtension\Traits;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;

trait FilterScopesTrait
{
    public array $appliedScopes = [];
    protected ?array $scopes    = [];

    public function addScopesFilter(?array $scopes = []): self
    {
        if (blank($this->getScopes())) {
            $this->setScopes($scopes);
        }

        if (app()->environment('testing') || !app()->runningInConsole()) {
            $this->setScopes(request(config('servicebase.parameters_default.filter'), []));
        }

        $this->getScopes()
            ->each(function ($scope, $key) {
                $nameFilter = str("by_{$key}")->camel()->toString();
                $nameScoped = str("scope_by_{$key}")->camel()->toString();
                if ((method_exists($this->getModel(), $nameScoped) || method_exists($this->getModel(), $nameFilter)) && $this->doesntUsedScope($nameFilter)) {
                    $this->defaultQuery()->$nameFilter();

                    $this->appliedScopes[] = $nameFilter;
                }
            });

        return $this;
    }

    public function setScopes(?array $scopes): self
    {
        if (app()->runningInConsole()) {
            $this->runningInConsole = true;
        }

        $this->scopes = $scopes;

        return $this;
    }

    public function getScopes(): Collection
    {
        return collect($this->scopes);
    }

    private function doesntUsedScope(string $scope): bool
    {
        if (!config('servicebase.prevent_scopes_duplicated', true)) {
            return true;
        }

        if (blank($this->appliedScopes)) {
            return true;
        }

        return collect($this->appliedScopes)->doesntContain($scope);
    }
}
