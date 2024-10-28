<?php

namespace GustavoSantarosa\ServiceBasicsExtension;

use GustavoSantarosa\HandlerBasicsExtension\Traits\ApiResponseTrait;
use GustavoSantarosa\PerPageTrait\PerPageTrait;
use GustavoSantarosa\ServiceBasicsExtension\Traits\FilterInclude;
use GustavoSantarosa\ValidateTrait\AutoDataTrait;
use GustavoSantarosa\ValidateTrait\ValidateTrait;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

abstract class BaseService
{
    use ValidateTrait;
    use PerPageTrait;
    use ApiResponseTrait;
    use AutoDataTrait;
    use FilterInclude;

    protected bool $paginationEnabled = true;
    protected bool $softDeleteEnabled = true;
    protected mixed $data             = null;
    protected bool $existsData        = false;
    protected bool $sync              = false;
    protected bool $qaTest            = false;
    protected $authUser;

    protected $model;

    protected ?Model $defaultModel;

    /**
     * Query applied to specified request.
     */
    private ?Builder $customQuery = null;

    protected array $searchableColumns = [];

    protected array $searchableRelations = [];

    protected $initializedAutoDataTrait = [
        'store',
        'update',
    ];

    public function __construct()
    {
        $this->bootIfNotBooted();

        $this->initializeTraits();

        if (!is_null($this->model)) {
            $this->defaultModel = new $this->model();
        }
    }

    public function index(): LengthAwarePaginator|Collection
    {
        $this->include();

        $query = $this->defaultQuery();

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

        if ('random' === request()->sortby) {
            $query->inRandomOrder();
        } else {
            $query->orderby(request()->sortby ?? 'id', request()->sort ?? 'asc');
        }

        $indexed = $this->result($query);

        $indexed->transform(function ($item) {
            foreach ($item->getRelations() as $index => $relation) {
                if (is_null($relation)) {
                    $item->$index();
                }
            }

            return $item;
        });

        return $indexed;
    }

    public function show(int $id): Model
    {
        $this->include();

        $showed = $this->defaultQuery()->findOrfail($id);

        foreach ($showed->getRelations() as $index => $relation) {
            if (is_null($relation)) {
                $showed->$index();
            }
        }

        return $showed;
    }

    public function store(): Model
    {
        $data = !$this->existsData ? request()->data() : $this->data;

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
        $data  = !$this->existsData ? request()->data() : $this->data;
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
        if (is_null($this->customQuery)) {
            $this->customQuery = $customQuery;
        }
    }

    public function setData(array|object $data): self
    {
        $this->existsData = true;
        $this->data       = $data;

        return $this;
    }

    public function setSegmentData(
        object $data,
        array $segmentAttributes,
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

    /**
     * The array of booted models.
     *
     * @var array
     */
    protected static $booted = [];

    /**
     * The array of trait initializers that will be called on each new instance.
     *
     * @var array
     */
    protected static $traitInitializers = [];

    /**
     * Check if the model needs to be booted and if so, do it.
     *
     * @return void
     */
    protected function bootIfNotBooted()
    {
        if (!isset(static::$booted[static::class])) {
            static::$booted[static::class] = true;

            static::booting();
            static::boot();
            static::booted();
        }
    }

    /**
     * Initialize any initializable traits on the model.
     *
     * @return void
     */
    protected function initializeTraits()
    {
        foreach (static::$traitInitializers[static::class] as $method) {
            $this->{$method}();
        }
    }

    /**
     * Perform any actions required before the model boots.
     *
     * @return void
     */
    protected static function booting()
    {
    }

    /**
     * Bootstrap the model and its traits.
     *
     * @return void
     */
    protected static function boot()
    {
        static::bootTraits();
    }

    /**
     * Perform any actions required after the model boots.
     *
     * @return void
     */
    protected static function booted()
    {
    }

    /**
     * Boot all of the bootable traits on the model.
     *
     * @return void
     */
    protected static function bootTraits()
    {
        $class = static::class;

        $booted = [];

        static::$traitInitializers[$class] = [];

        foreach (class_uses_recursive($class) as $trait) {
            $method = 'boot'.class_basename($trait);

            if (method_exists($class, $method) && !in_array($method, $booted)) {
                forward_static_call([$class, $method]);

                $booted[] = $method;
            }

            if (method_exists($class, $method = 'initialize'.class_basename($trait))) {
                static::$traitInitializers[$class][] = $method;

                static::$traitInitializers[$class] = array_unique(
                    static::$traitInitializers[$class]
                );
            }
        }
    }

    /**
     * When a model is being unserialized, check if it needs to be booted.
     *
     * @return void
     */
    public function __wakeup()
    {
        $this->bootIfNotBooted();

        $this->initializeTraits();
    }
}
