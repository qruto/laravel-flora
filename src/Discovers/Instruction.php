<?php

namespace Qruto\Flora\Discovers;

use Closure;
use Qruto\Flora\Enums\Environment;
use Qruto\Flora\Enums\FloraType;

class Instruction
{
    public function __construct(
        protected array|Closure|null $install = null,
        protected array|Closure|null $update = null,
        public ?string $assetsTag = null,
    ) {
    }

    public function get(FloraType $type, ?Environment $environment = null): Closure
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
