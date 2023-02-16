<?php

namespace Qruto\Formula\Discovers;

use Closure;
use Qruto\Formula\Enums\Environment;
use Qruto\Formula\Enums\FormulaType;

class Instruction
{
    public function __construct(
        protected array|Closure|null $install = null,
        protected array|Closure|null $update = null,
        public string|null $assetsTag = null,
    ) {
    }

    public function get(FormulaType $type, ?Environment $environment = null): Closure
    {
        if (is_null($environment)) {
            $environment = Environment::Production;
        }

        $typeValue = $type->value;

        if (is_null($this->$typeValue)) {
            return static fn () => null;
        }

        if (is_array($this->$typeValue)) {
            if (isset($this->$typeValue[$environment->value])) {
                return $this->$typeValue[$environment->value];
            }

            return static fn () => null;
        }

        return $this->$typeValue;
    }
}
