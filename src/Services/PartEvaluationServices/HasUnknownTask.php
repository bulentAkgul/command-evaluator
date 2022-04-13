<?php

namespace Bakgul\Evaluator\Services\PartEvaluationServices;

use Bakgul\Evaluator\Evaluator;
use Bakgul\Evaluator\Tasks\SetDefaultMessage;
use Bakgul\Kernel\Functions\CollectTypes;
use Bakgul\Kernel\Helpers\Arry;
use Bakgul\Kernel\Helpers\Settings;
use Bakgul\Kernel\Helpers\Isolation;
use Illuminate\Support\Arr;

class HasUnknownTask extends Evaluator
{
    const KEY = 'task';
    const TYPE = 'unknown';

    private static $issue;

    public static function handle(array $request): ?array
    {
        return parent::evaluatePart(get_called_class(), $request);
    }

    public static function args($request): array
    {
        return [self::KEY, self::TYPE, $request, self::$issue, false, SetDefaultMessage::class];
    }

    public static function hasIssue(array $request): bool
    {
        self::findIssues($request);

        return !empty(self::$issue);
    }

    private static function findIssues($request)
    {
        self::$issue = array_filter(array_diff(self::isolateTasks($request), self::getTasks($request)));
    }

    private static function isolateTasks(array $request): array
    {
        return array_filter(self::produce(
            self::produce(explode('+', $request['name']), fn ($x) => explode(',', $x)),
            fn ($x) => Isolation::tasks($x)
        ));
    }

    private static function getTasks(array $request)
    {
        return self::produce(
            self::getTypes($request['type']),
            fn ($x) => self::setTasks($request['name'], $x)
        );
    }

    private static function getTypes(string $type): array
    {
        return Arry::unique(array_map(
            fn ($x) => $x['type'],    
            Arr::flatten(CollectTypes::_($type), 1)
        ));
    }
    
    private static function setTasks(string $name, string $type)
    {
        return array_filter(array_merge(
            str_contains($name, Settings::seperators('modifier') . 'all') ? ['all'] : [],
            Settings::files("{$type}.tasks")
        ));
    }

    private static function isolate(string $method, string $value): array
    {
        return self::produce(
            explode(Settings::seperators('part'), $value),
            fn ($x) => str_contains($x, 'all') ? [] : Isolation::$method($x)
        );
    }

    private static function produce(array $array, callable $callback)
    {
        return Arry::unique(Arr::flatten(array_map($callback, $array)));
    }
}
