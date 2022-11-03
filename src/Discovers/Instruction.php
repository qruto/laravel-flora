<?php

namespace Qruto\Initializer\Discovers;

use Closure;
use Qruto\Initializer\Enums\Environment;
use Qruto\Initializer\Enums\InitializerType;

class Instruction
{
    public function __construct(
        protected array|Closure|null $install = null,
        protected array|Closure|null $update = null,
    ) {
    }

    public function get(InitializerType $type, ?Environment $environment): Closure
    {
        if (is_null($environment)) {
            $environment = Environment::Production;
        }

        $typeValue = $type->value;

        if (is_null($this->$typeValue)) {
            return fn () => null;
        }

        if (is_array($this->$typeValue)) {
            if (isset($this->$typeValue[$environment->value])) {
                return $this->$typeValue[$environment->value];
            }

            return fn () => null;
        }

        return $this->$typeValue;
    }
}
