<?php

namespace Bakgul\Evaluator\Services\PartEvaluationServices;

use Bakgul\Kernel\Helpers\Arry;
use Bakgul\Kernel\Helpers\Settings;
use Bakgul\Evaluator\Evaluator;
use Bakgul\Evaluator\Tasks\SetUnmodifiedMessage;

class HasUnknownRelation extends Evaluator
{
    const KEY = 'relation';
    const TYPE = 'unknown';

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
        return Arry::hasNot($request['relation'], Settings::code('relations.types'), 'both');
    }
}
