<?php

namespace Bakgul\Evaluator\Services\PartEvaluationServices;

use Bakgul\Kernel\Helpers\Arry;
use Bakgul\Kernel\Helpers\Isolation;
use Bakgul\Kernel\Helpers\Settings;
use Bakgul\Kernel\Tasks\CollectTypes;
use Bakgul\Evaluator\Evaluator;
use Bakgul\Evaluator\Tasks\GetMessage;
use Bakgul\Evaluator\Tasks\SetUnmodifiedMessage;
use Illuminate\Support\Arr;

class HasConflictingType extends Evaluator
{
    const KEY = 'type';
    const TYPE = 'conflicting';

    private static $issue;
    private static $confirmations;

    public static function handle(array $request, array $confirmations): ?array
    {
        if ($request['relation'] == 'mtm') return null;

        self::$confirmations = $confirmations;

        return parent::evaluatePart(get_called_class(), $request);
    }

    public static function args($request): array
    {
        return [self::KEY, self::TYPE, $request, self::$issue, true, null];
    }

    public static function hasIssue(array $request): bool
    {
        return in_array(self::KEY . '.' . self::TYPE, self::$confirmations)
            ? false
            : $request['polymorphic'] && $request['mediator'];
    }

    public static function getMessage($request)
    {
        return GetMessage::_(self::setKey(), self::setRequest($request));
    }

    private static function setKey()
    {
        return implode(Settings::seperators('addition'), [self::KEY, self::TYPE]);
    }

    private static function setRequest(array $request)
    {
        return [
            ...$request,
            'count' => $request['relation'] == 'oto' ? 'One' : 'Many',
        ];
    }
}
