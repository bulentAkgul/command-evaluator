<?php

namespace Bakgul\Evaluator\Services;

use Bakgul\Evaluator\Evaluator;
use Bakgul\Evaluator\Services\PartEvaluationServices\{
    HasDuplicatedPackage, HasMissingPackage, HasUnknownRoot, RunsInUnsuitableStandalone
};

class PackageCommandEvaluationService extends Evaluator
{
    public static function handle(array $request, array $confirmations): ?array
    {
        $evaluation = null;

        foreach (self::evaluators() as $evaluator) {
            $evaluation = $evaluator::handle($request, $confirmations);
            
            if ($evaluation) return $evaluation;
        }

        return $evaluation;
    }

    private static function evaluators()
    {
        return [
            RunsInUnsuitableStandalone::class,
            HasMissingPackage::class,
            HasDuplicatedPackage::class,
            HasUnknownRoot::class
        ];
    }
}
