<?php

namespace Bakgul\Evaluator\Tests\Feature\CommandPartEvaluationTests;

use Bakgul\Kernel\Tests\Concerns\HasTestMethods;
use Bakgul\Evaluator\Services\PartEvaluationServices\HasUnknownRoot;
use Bakgul\Evaluator\Tests\EvaluatorTestMethods;
use Bakgul\Kernel\Tests\Tasks\SetupTest;

class UnknownRootTest extends EvaluatorTestMethods
{
    use HasTestMethods;
    
    private $evaluator;

    public function __construct()
    {
        $this->evaluator = HasUnknownRoot::class;

        parent::__construct();
    }

    /** @test */
    public function evaluator_will_return_null_when_create_package_command_has_a_valid_root()
    {
        $this->testPackage = (new SetupTest)([false, false]);

        $this->assertNull($this->evaluator::handle($this->setRequest(key: 'package')));
    }

    /** @test */
    public function evaluator_will_return_error_when_create_package_command_has_an_invalid_root()
    {
        $response = $this->evaluator::handle($this->setRequest(['root' => 'xxx'], 'package'));

        $this->assertNotNull($response);

        $this->assertTrue($response['key'] == 'root');
        $this->assertTrue($response['evaluated'] == 'unknown');
        $this->assertTrue($response['is_confirmable'] == false);
        $this->assertTrue(str_contains($response['message'], 'Unknown root: xxx'));
    }
}
