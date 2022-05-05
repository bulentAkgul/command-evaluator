<?php

namespace Bakgul\Evaluator\Tasks;

use Bakgul\Kernel\Helpers\Settings;
use Bakgul\Kernel\Helpers\Isolation;

class FindParentIssue
{
    public function __invoke($type)
    {
        return array_filter(array_map(
            fn ($x) => $this->isIssue($x[1]) ? $x : [],
            Isolation::types($type)
        ));
    }

    private function isIssue(string $variation)
    {
        return $variation && in_array($variation, array_keys(Settings::needs('parent')));
    }
}
