<?php

declare(strict_types = 1);

namespace QuantumTecnology\ServiceBasicsExtension\Traits;

use Illuminate\Http\Response;
use Illuminate\Support\Collection;

trait FilterScopesTrait
{
    public array $appliedScopes      = [];
    protected ?array $scopes         = [];
    protected ?array $allowedFilters = [];

    public function addScopesFilter(?array $scopes = []): self
    {
        $this->runningInConsole = app()->runningInConsole();

        $this->setScopes($scopes);

        if (app()->environment('testing') || !$this->runningInConsole) {
            $this->setScopes(array_keys(request(config('servicebase.parameters_default.filter'), [])));
        }

        $this->getScopes()
            ->each(function ($scope, $key) {
                $scope = $this->renameScope($scope);

                $this->defaultQuery()
                    ->when(
                        $this->canApplyScope($scope),
                        fn ($query) => tap($query->$scope(), fn () => $this->appliedScopes[] = $scope),
                    );
            });

        return $this;
    }

    public function setScopes(array | string $scopes): self
    {
        if (is_string($scopes)) {
            $scopes = explode(',', $scopes);
        }

        $this->scopes = collect($scopes)
            ->merge($this->scopes ?? [])
            ->filter(function ($scope) {
                if (!in_array($scope, $this->getAllowedFilters())) {
                    abort_if(config('servicebase.force_throw', false), Response::HTTP_FORBIDDEN, "The scope '{$scope}' is not allowed.");

                    return false;
                }

                return true;
            })
            ->merge(
                collect($scopes)
                    ->transform(fn ($scope) => str("by_{$scope}")->camel()->toString())
            )
            ->merge(
                collect($scopes)
                    ->transform(fn ($scope) => str("scope_by_{$scope}")->camel()->toString())
            )
            ->filter(fn ($scope) => method_exists($this->getModel(), $scope))

            ->unique()
            ->values()
            ->all();

        return $this;
    }

    public function getScopes(): Collection
    {
        return collect($this->scopes);
    }

    public function getAllowedFilters(): array
    {
        return $this->allowedFilters ?? [];
    }

    public function setAllowedFilters(array $allowedFilters): void
    {
        $this->allowedFilters = array_values(array_unique(array_merge($this->getAllowedFilters(), $allowedFilters)));
    }

    protected function renameScope(string $scope): string
    {
        if (str_starts_with($scope, 'scopeBy')) {
            $scope = lcfirst(mb_substr($scope, 5));
        }

        return $scope;
    }

    private function canApplyScope(?string $scope): bool
    {

        if (blank($scope)) {
            return false;
        }

        if (config('servicebase.prevent_scopes_duplicated', true)) {
            return collect($this->appliedScopes)->doesntContain($scope);
        }

        return true;
    }
}
