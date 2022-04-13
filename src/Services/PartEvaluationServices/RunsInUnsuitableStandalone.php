<?php

namespace Bakgul\Evaluator\Services\PartEvaluationServices;

use Bakgul\Kernel\Helpers\Arry;
use Bakgul\Kernel\Helpers\Folder;
use Bakgul\Kernel\Helpers\Settings;
use Bakgul\Evaluator\Evaluator;
use Bakgul\Evaluator\Tasks\SetUnmodifiedMessage;

class RunsInUnsuitableStandalone extends Evaluator
{
    const KEY = 'standalone';
    const TYPE = 'unsuitable';

    private static $request;

    public static function handle(array $request): ?array
    {
        self::$request = $request;

        return parent::evaluatePart(get_called_class(), $request, 'package');
    }

    public static function args($request): array
    {
        return [self::KEY, self::TYPE, self::$request, null, false, SetUnmodifiedMessage::class];
    }

    public static function hasIssue(array $request): bool
    {
        if (Settings::standalone('laravel')) {
            self::$request['command'] = 'code:laravel';
            return true;
        }

        if (self::isSecondTime()) {
            self::$request['command'] = 'code:package';
            return true;
        }
        
        return false;
    }

    private static function isSecondTime(): bool
    {
        if (!Settings::standalone('package')) return false;

        if (file_exists(base_path('src')) && Arry::contains('ServiceProvider', Folder::content(base_path('src')))) return true;

        return false;
    }
}