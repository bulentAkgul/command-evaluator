<?php

namespace Bakgul\Evaluator\Tasks;

use Bakgul\Kernel\Helpers\Settings;

class SetParentMessage
{
    public function __invoke($key, $type, $request, $issue)
    {
        return GetMessage::_(
            implode('.', [$key, $type]),
            $this->modifyRequest($request, $issue)
        );
    }

    private function modifyRequest(array $request, array $issue): array
    {
        $request['variation'] = implode(', ', $this->stringify($issue));
        $request['file'] = Settings::needs("parent.{$issue[0][1]}");

        return $request;
    }

    private function stringify($issue)
    {
        return array_map(fn ($x) => implode(Settings::seperators('modifier'), array_filter($x)), $issue);
    }
}
