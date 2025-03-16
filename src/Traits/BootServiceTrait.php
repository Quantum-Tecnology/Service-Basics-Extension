<?php

namespace QuantumTecnology\ServiceBasicsExtension\Traits;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait BootServiceTrait
{
    protected bool $paginationEnabled = true;
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
    private ?Builder $query       = null;

    protected array $searchableColumns = [];

    protected array $searchableRelations = [];

    protected $initializedAutoDataTrait = [
        'store',
        'update',
    ];

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

    public function __construct()
    {
        $this->bootIfNotBooted();

        $this->initializeTraits();

        if (!is_null($this->model)) {
            $this->defaultModel = new $this->model();
        }
    }

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
                    static::$traitInitializers[$class],
                );
            }
        }
    }

    public function setModel(string|Model $value): self
    {
        $this->defaultModel = $value instanceof Model ? $value : new $value();

        return $this;
    }

    public function getModel(): Model
    {
        return $this->defaultModel;
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

    /**
     * Default query.
     */
    protected function defaultQuery(): Builder
    {
        $this->query = $this->query ?? $this->getModel()::query();

        return $this->query;
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
