<?php

namespace Bakgul\Evaluator\Services\PartEvaluationServices;

use Bakgul\Kernel\Helpers\Arry;
use Bakgul\Kernel\Helpers\Settings;
use Bakgul\Kernel\Helpers\Isolation;
use Bakgul\Evaluator\Evaluator;
use Bakgul\Evaluator\Tasks\SetDefaultMessage;

class HasUnknownRole extends Evaluator
{
    const KEY = 'role';
    const TYPE = 'unknown';

    private static $issue = [];

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
        self::findIssues($request['type']);

        return !empty(self::$issue);
    }

    private static function findIssues($type)
    {
        self::$issue = Arry::unique([...self::$issue, ...self::issuedRoles($type)]);
    }

    private static function issuedRoles($type): array
    {
        return array_values(array_filter(array_map(
            fn ($x) => self::isValid($x) ? '' : $x[2],
            self::getTypes($type)
        )));
    }

    private static function getTypes($type): array
    {
        return array_filter(
            Isolation::types($type),
            fn ($x) => Settings::files("{$x[0]}.roles")
        );
    }

    private static function isValid(array $type): bool
    {
        return in_array($type[2], Settings::files("{$type[0]}.roles"));
    }
}
