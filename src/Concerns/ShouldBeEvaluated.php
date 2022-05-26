<?php

namespace Bakgul\Evaluator\Concerns;

trait ShouldBeEvaluated
{
    public $evaluator;
    public ?array $evaluation;
    public array $confirmations = [];
    public int $attemptLimit = 5;
    public int $hasAttempted = 0;
    public string $terminate = 'terminate_command_execution';

    public function setEvaluator($evaluator)
    {
        $this->evaluator = $evaluator;
    }

    public function evaluate(): void
    {
        $this->try();

        while ($this->shouldBeEvaluated()) {
            $this->demand();

            if ($this->isTerminated()) return;

            $this->try();
        }
    }

    public function try()
    {
        $this->evaluation = $this->evaluator::handle($this->request, $this->confirmations);

        $this->hasAttempted++;
    }

    public function shouldBeEvaluated(): bool
    {
        return  $this->evaluation && !$this->isTerminated() && $this->hasChance();
    }

    public function isTerminated(): bool
    {
        return $this->evaluation['is_confirmable']
            && $this->request[$this->evaluation['key']] == $this->terminate;
    }

    public function stop(): bool
    {
        return $this->evaluation && ($this->isTerminated() || !$this->hasChance());
    }

    public function hasChance(): bool
    {
        return $this->hasAttempted < $this->attemptLimit;
    }

    public function demand(): void
    {
        if (!$this->evaluation['is_confirmable']) return;

        $this->request[$this->evaluation['key']] = $this->apply($this->ask($this->evaluation['message']));
    }

    private function apply($reply)
    {
        if ($reply === null) return '';

        return $this->isConfirmation($reply)
            ? $this->setConfirmation($reply)
            : $this->setNewValue($reply);
    }

    public function isConfirmation(string $reply)
    {
        return $this->evaluation['is_confirmable'] && in_array(strtolower($reply), ['y', 'n']);
    }

    public function setConfirmation(string $reply)
    {
        if (trim(strtolower($reply)) == 'n') return $this->terminate;

        $this->confirmations[] = "{$this->evaluation['key']}.{$this->evaluation['evaluated']}";

        return $this->request[$this->evaluation['key']];
    }

    public function setNewValue(string $reply)
    {
        return $this->evaluation['problem'] && is_string($this->evaluation['problem'])
            ? str_replace($this->evaluation['problem'], $reply, $this->request[$this->evaluation['key']])
            : $reply;
    }

    public function terminate()
    {
        $this->error(
            "Command has been terminated due to the {$this->evaluation['key']} issue. {$this->getMessage()}"
        );
    }

    public function getMessage()
    {
        return $this->evaluation['is_confirmable'] ? '' : " {$this->evaluation['message']}";
    }
}
