<?php

namespace Bakgul\Evaluator\Services\PartEvaluationServices;

use Bakgul\Kernel\Helpers\Isolation;
use Bakgul\Kernel\Helpers\Settings;
use Bakgul\Kernel\Helpers\Text;
use Bakgul\Kernel\Tasks\CollectFiles;
use Bakgul\Evaluator\Evaluator;
use Bakgul\Evaluator\Tasks\SetDefaultMessage;
use Bakgul\Kernel\Helpers\Convention;

class HasUnknownModel extends Evaluator
{
    const KEY = 'model';
    const TYPE = 'unknown';
    const KEYS = ['from', 'to'];

    private static $issue;

    public static function handle(array $request): ?array
    {
        self::$issue = [];
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

    private static function findIssues(array $request): void
    {
        $models = self::collectModels($request);

        foreach (self::extractModels($request) as $model) {
            if (self::isUnknown($model, $models)) {
                self::$issue[] = Text::prepend($model['package']) . $model['name'];
            }
        }

        self::$issue = array_unique(self::$issue);
    }

    private static function collectModels(array $request): array
    {
        $models = [];
        $allModels = [];

        foreach (self::KEYS as $key) {
            $package = Isolation::subs($request[$key]);

            if (!$package) {
                $allModels = empty($allModels) ? CollectFiles::_('model') : $allModels;
                $models[$key] = $allModels;
            } else {
                $models[$key] = CollectFiles::_('model', $package);
            }
        }

        return $models;
    }

    private static function extractModels($request): array
    {
        return array_map(fn ($x) => [
            'key' => $x,
            'package' => Isolation::subs($request[$x]),
            'name' => Convention::class(Isolation::name($request[$x]))
        ], self::KEYS);
    }

    private static function isUnknown($model, $models): bool
    {
        $filteredByName = array_filter(
            $models[$model['key']],
            fn ($x) => Text::getTail($x) == "{$model['name']}.php"
        );

        if (empty($filteredByName)) return true;

        if (!$model['package'] || Settings::standalone()) return false;

        return empty(array_filter($filteredByName, fn ($x) => str_contains($x, Text::wrap($model['package']))));
    }
}
