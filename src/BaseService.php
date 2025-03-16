<?php

namespace QuantumTecnology\ServiceBasicsExtension;

use QuantumTecnology\HandlerBasicsExtension\Traits\ApiResponseTrait;
use QuantumTecnology\PerPageTrait\PerPageTrait;
use QuantumTecnology\ServiceBasicsExtension\Traits\BootServiceTrait;
use QuantumTecnology\ServiceBasicsExtension\Traits\DestroyServiceTrait;
use QuantumTecnology\ServiceBasicsExtension\Traits\FilterIncludeTrait;
use QuantumTecnology\ServiceBasicsExtension\Traits\IndexServiceTrait;
use QuantumTecnology\ServiceBasicsExtension\Traits\RestoreServiceTrait;
use QuantumTecnology\ServiceBasicsExtension\Traits\SetSegmentDataTrait;
use QuantumTecnology\ServiceBasicsExtension\Traits\ShowServiceTrait;
use QuantumTecnology\ServiceBasicsExtension\Traits\StoreServiceTrait;
use QuantumTecnology\ServiceBasicsExtension\Traits\UpdateServiceTrait;
use QuantumTecnology\ValidateTrait\AutoDataTrait;
use QuantumTecnology\ValidateTrait\ValidateTrait;

abstract class BaseService
{
    use ValidateTrait;
    use PerPageTrait;
    use ApiResponseTrait;
    use AutoDataTrait;
    use FilterIncludeTrait;
    use SetSegmentDataTrait;
    use IndexServiceTrait;
    use ShowServiceTrait;
    use StoreServiceTrait;
    use UpdateServiceTrait;
    use DestroyServiceTrait;
    use RestoreServiceTrait;
    use BootServiceTrait;
}
