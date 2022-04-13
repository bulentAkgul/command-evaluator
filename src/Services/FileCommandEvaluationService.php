<?php

namespace Bakgul\Evaluator\Services;

use Bakgul\Evaluator\Evaluator;
use Bakgul\Evaluator\Services\PartEvaluationServices\{
    HasConflictingParent, HasDuplicatedType, HasMissingApp, HasMissingPackage, HasMissingParent, HasUnknownType, HasUnknownApp, HasUnknownPackage, HasUnknownTask, HasUnsuitableVariation
};

class FileCommandEvaluationService extends Evaluator
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
            HasMissingPackage::class,
            HasUnknownPackage::class,
            HasMissingApp::class,
            HasUnknownApp::class,
            HasDuplicatedType::class,
            HasUnknownType::class,
            HasUnsuitableVariation::class,
            HasUnknownTask::class,
            HasConflictingParent::class,
            HasMissingParent::class,
        ];
    }
}
