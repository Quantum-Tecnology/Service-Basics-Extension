<?php

namespace QuantumTecnology\ServiceBasicsExtension\Traits;

trait FilterIncludeTrait
{
    protected array|string $dataIncludes = [];

    private $includeCallable;

    private function addIncludeFilter(?string $includes = ''): void
    {
        if (blank($this->dataIncludes = request(config('servicebase.parameters_default.includes'), $includes))) {
            return;
        }

        if (!is_callable($this->includeCallable)) {
            $this->setInclude();
        }

        $callback = $this->includeCallable;

        $this->query = $callback($this->defaultQuery());
    }

    /**
     * With this function it is possible to pass a callback to include the custom relationships.
     */
    protected function setInclude(?callable $callback = null): void
    {
        if (is_callable($callback)) {
            $this->includeCallable = $callback;

            return;
        }

        $this->includeCallable = function ($query) {
            collect(explode(',', $this->dataIncludes))
                ->each(function ($relation) use ($query) {
                    $relation = collect(explode(';', $relation))
                        ->transform(function ($collum) use ($query) {
                            if ('count' === $collum) {
                                $query->withCount(explode(':', $collum)[0]);

                                return;
                            }

                            return $collum;
                        })->filter();

                    if ($relation->isEmpty()) {
                        return;
                    }

                    $query->with($relation->implode(','));
                });

            return $query;
        };

        return;
    }
}
