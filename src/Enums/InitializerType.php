<?php

namespace Qruto\Initializer\Enums;

enum InitializerType: string
{
    case Install = 'install';
    case Update = 'update';
}
