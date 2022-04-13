<?php

namespace Bakgul\Evaluator\Tasks;

use Illuminate\Support\Str;

class SetDefaultMessage
{
    public function __invoke($key, $type, $request, $issue)
    {
        return GetMessage::_(
            implode('.', ['default', $type]),
            $this->modifyRequest($key, $request, $issue)
        );
    }

    private function modifyRequest($key, $request, $issue): array
    {
        $request['identifier'] = count($issue) > 1 ? Str::plural($key) : $key;
        $request['issues'] = implode(', ', $issue);

        return $request;
    }
}
