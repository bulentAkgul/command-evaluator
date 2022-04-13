<?php

namespace Bakgul\Evaluator\Services\PartEvaluationServices;

use Bakgul\Evaluator\Evaluator;
use Bakgul\Evaluator\Tasks\SetUnmodifiedMessage;
use Bakgul\Kernel\Helpers\Isolation;

class HasMissingExtra extends Evaluator
{
    const KEY = 'extra';
    const TYPE = 'missing';

    public static function handle(array $request): ?array
    {
        return parent::evaluatePart(get_called_class(), $request);
    }

    public static function args($request): array
    {
        return [self::KEY, self::TYPE, $request, null, false, SetUnmodifiedMessage::class];
    }

    public static function hasIssue(array $request): bool
    {
        return str_contains($request['type'], 'view') && !$request['app'] && !Isolation::extra($request['type']);
    }
}
