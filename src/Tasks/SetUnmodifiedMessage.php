<?php

namespace Bakgul\Evaluator\Tasks;

class SetUnmodifiedMessage
{
    public function __invoke($key, $type, $request)
    {
        return GetMessage::_(implode('.', [$key, $type]), $request);
    }
}
