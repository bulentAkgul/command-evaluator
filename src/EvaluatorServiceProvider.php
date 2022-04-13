<?php

namespace Bakgul\Evaluator;

use Bakgul\Kernel\Concerns\HasConfig;
use Illuminate\Support\ServiceProvider;

class EvaluatorServiceProvider extends ServiceProvider
{
    use HasConfig;
    
    public function boot()
    {
        //
    }

    public function register()
    {
        $this->registerConfigs(__DIR__ . DIRECTORY_SEPARATOR . '..');
    }
}
