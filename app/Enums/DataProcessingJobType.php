<?php

namespace App\Enums;

enum DataProcessingJobType: string
{
    case IMPORT = 'import';
    case EXPORT = 'export';
}
