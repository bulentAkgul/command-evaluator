<?php

namespace Bakgul\Evaluator\Services;

use Bakgul\Kernel\Helpers\Arry;
use Bakgul\Kernel\Helpers\Isolation;
use Bakgul\Evaluator\Evaluator;
use Bakgul\Evaluator\Services\PartEvaluationServices\{
    HasConflictingRelation, HasExcessMediator, HasUnknownModel, HasUnknownPackage, HasUnknownRelation, HasUnuseableKey
};

class RelationCommandEvaluationService extends Evaluator
{
    public static function handle(array $request, array $confirmations): ?array
    {
        $evaluation = null;

        $request['package'] = self::collectPackages($request);

        foreach (self::evaluators() as $evaluator) {
            $evaluation = $evaluator::handle($request, $confirmations);
            
            if ($evaluation) return $evaluation;
        }

        return $evaluation;
    }

    private static function evaluators()
    {
        return [
            HasUnknownRelation::class,
            HasUnknownPackage::class,
            HasUnknownModel::class,
            HasConflictingRelation::class,
            HasExcessMediator::class,
            HasUnuseableKey::class
        ];
    }

    private static function collectPackages(array $request)
    {
        $packages = [];

        foreach (['from', 'to', 'mediator'] as $key) {
            $package = Isolation::package($request[$key]);
            $packages[] = $package != $request[$key] ? $package : '';
        }

        return implode(',', Arry::unique(array_filter($packages)));
    }
}
