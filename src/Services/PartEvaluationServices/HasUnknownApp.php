<?php

namespace Bakgul\Evaluator\Services\PartEvaluationServices;

use Bakgul\Kernel\Tasks\MutateApp;
use Bakgul\Evaluator\Evaluator;
use Bakgul\Evaluator\Tasks\SetUnmodifiedMessage;

class HasUnknownApp extends Evaluator
{
    const KEY = 'app';
    const TYPE = 'unknown';

    private static $confirmations;

    public static function handle(array $request, array $confirmations): ?array
    {
        if (parent::isWarningsDisabled()) return null;

        self::$confirmations = $confirmations;

        return parent::evaluatePart(get_called_class(), $request);
    }

    public static function args($request): array
    {
        return [self::KEY, self::TYPE, $request, null, true, SetUnmodifiedMessage::class];
    }

    public static function hasIssue(array $request): bool
    {
        return !in_array(self::KEY . '.' . self::TYPE, self::$confirmations)
            && !in_array($request[self::KEY], MutateApp::get())
            && $request[self::KEY];
    }
}
