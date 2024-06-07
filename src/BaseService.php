<?php

namespace GustavoSantarosa\ServiceBasicsExtension;

use GustavoSantarosa\HandlerBasicsExtension\Traits\ApiResponseTrait;
use GustavoSantarosa\PerPageTrait\PerPageTrait;
use GustavoSantarosa\ValidateTrait\ValidateTrait;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BaseService
{
    use ValidateTrait;
    use PerPageTrait;
    use ApiResponseTrait;

    protected bool $paginationEnabled = true;
    protected bool $softDeleteEnabled = true;
    protected mixed $data             = null;
    protected bool $existsData        = false;
    protected bool $sync              = false;
    protected bool $qaTest            = false;
    protected $authUser;
    protected ?Model $defaultModel;

    /**
     * Query applied to specified request.
     */
    private ?Builder $customQuery = null;

    protected array $searchableColumns = [];

    protected array $searchableRelations = [];

    public function index(): LengthAwarePaginator|Collection
    {
        $query = $this->defaultQuery();

        if (request()->include) {
            $query->with(explode(',', request()->include));
        }

        if (
            isset(request()->filter['trashed'])
            && 'only' === request()->filter['trashed']
            && true === $this->softDeleteEnabled
        ) {
            $query->onlyTrashed();
        }

        if (!empty(request()->search) && (count($this->searchableColumns) > 0 || count($this->searchableRelations) > 0)) {
            $query->where(function ($subQuery) {
                $searchString = str_replace(' ', '%', trim(request()->search));

                $subQuery->where(function ($query) use ($searchString) {
                    foreach ($this->searchableColumns as $column) {
                        $query->orWhere($column, 'LIKE', "%{$searchString}%");
                    }
                });

                foreach ($this->searchableRelations as $relation => $columns) {
                    $subQuery->orWhereHas($relation, function ($relationQuery) use ($columns, $searchString) {
                        $relationQuery->where(function ($query) use ($columns, $searchString) {
                            foreach ($columns as $relationColumn) {
                                $query->orWhere($relationColumn, 'LIKE', "%{$searchString}%");
                            }
                        });
                    });
                }
            });
        } elseif (!empty(request()->search) && 0 === count($this->searchableColumns) && 0 === count($this->searchableRelations)) {
            $this->unprocessableEntityException('Parameter search not enabled this route.');
        }

        $query->orderby(request()->sortby ?? 'id', request()->sort ?? 'asc');

        return $this->result($query);
    }

    public function show(int $id): Model
    {
        $query = $this->defaultQuery();

        if (request()->include) {
            $query->with(explode(',', request()->include));
        }

        return $query->findOrfail($id);
    }

    public function store(): Model
    {
        $data = !$this->existsData ? $this->validate() : $this->data;

        $transaction = DB::transaction(function () use ($data) {
            $callback = $this->defaultModel->create($data->toArray());

            foreach ($data as $indice => $value) {
                if (is_array($value)) {
                    $indice = Str::Camel($indice);
                    $callback->$indice()->sync($value);
                }
            }

            return $callback->refresh();
        });

        return $transaction;
    }

    public function update(int $id): Model
    {
        $data  = !$this->existsData ? $this->validate() : $this->data;
        $model = $this->show($id);

        $transaction = DB::transaction(function () use ($data, $model) {
            $model->update($data->toArray());

            foreach ($data as $indice => $value) {
                if (is_array($value)) {
                    $model->$indice()->sync($value, $this->sync);
                }
            }

            return $model->refresh();
        });

        return $transaction;
    }

    public function destroy(int $id): bool
    {
        $user = $this->show($id);

        return $user->delete();
    }

    public function restore(int $id): bool
    {
        $query = $this->defaultQuery();

        $model = $query->onlyTrashed()->findOrfail($id);

        return $model->restore();
    }

    public function setModel(string|Model $value): self
    {
        $this->defaultModel = $value instanceof Model ? $value : new $value();

        return $this;
    }

    public function setExistsData(bool $value): self
    {
        $this->existsData = $value;

        return $this;
    }

    public function setPaginationEnabled(bool $value): self
    {
        $this->paginationEnabled = $value;

        return $this;
    }

    public function setSync(bool $value): self
    {
        $this->sync = $value;

        return $this;
    }

    public function setAuthUser($value): self
    {
        $this->authUser = $value;

        return $this;
    }

    public function setQaTest(bool $value): self
    {
        $this->qaTest = $value;

        return $this;
    }

    public function getModel(): Model
    {
        return $this->defaultModel;
    }

    /**
     * Default query.
     */
    protected function defaultQuery(): Builder
    {
        $query = $this->customQuery ?? $this->defaultModel::query();

        return $query;
    }

    public function setCustomQuery(Builder $customQuery): void
    {
        $this->customQuery = $customQuery;
    }

    public function setData(array|object $data): self
    {
        $this->existsData = true;
        $this->data       = $data;

        return $this;
    }

    public function setSegmentData(
        object $data,
        array $segmentAttributes
    ): Collection {
        foreach ($segmentAttributes as $attribute) {
            if (isset($data->$attribute)) {
                $segment[$attribute] = $data->$attribute;
                unset($data->$attribute);
            }
        }

        $this->setData($data);

        return new Collection($segment ?? []);
    }
}
