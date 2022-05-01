<?php

namespace Bakgul\Evaluator\Services\PartEvaluationServices;

use Bakgul\Kernel\Helpers\Arry;
use Bakgul\Kernel\Helpers\Isolation;
use Bakgul\Kernel\Helpers\Settings;
use Bakgul\Kernel\Tasks\CollectTypes;
use Bakgul\Evaluator\Evaluator;
use Bakgul\Evaluator\Tasks\SetUnmodifiedMessage;
use Illuminate\Support\Arr;

class HasMissingApp extends Evaluator
{
    const KEY = 'app';
    const TYPE = 'missing';

    private static $issue;
    private static $confirmations;

    public static function handle(array $request, array $confirmations): ?array
    {
        if ($request['app'] || parent::isWarningsDisabled()) return null;

        self::$confirmations = $confirmations;

        return parent::evaluatePart(get_called_class(), $request);
    }

    public static function args($request): array
    {
        return [self::KEY, self::TYPE, $request, self::$issue, true, SetUnmodifiedMessage::class];
    }

    public static function hasIssue(array $request): bool
    {
        self::$issue = self::findIssues($request);

        return self::$issue !== null;
    }

    private static function findIssues(array $request)
    {
        if (in_array(self::KEY . '.' . self::TYPE, self::$confirmations)) return null;
        
        $mainTypes = Arr::pluck(Isolation::types($request['type']), 0);

        $needApp = self::typesNeedApp($mainTypes);
        
        if (!empty($needApp)) return $needApp;

        if (self::allowMissingApp()) return null;

        $relatedTypes = Arry::unique(array_diff(Arr::pluck(CollectTypes::_($request['type']), 'type'), $mainTypes));

        $needApp = self::typesNeedApp($relatedTypes);

        return empty($needApp) ? null : $needApp;
    }

    private static function allowMissingApp()
    {
        return Settings::evaluator('disable_warnings_unless_a_new_value_can_be_provided');
    }

    private static function typesNeedApp(array $types)
    {
        return array_filter($types, fn ($x) => str_contains(Settings::files("{$x}.path_schema"), '{{ app }}'));
    }
}
