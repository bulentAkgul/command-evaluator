<?php

namespace Bakgul\Evaluator\Tests\Feature\CommandPartEvaluationTests;

use Bakgul\Kernel\Tests\Concerns\HasTestMethods;
use Bakgul\Evaluator\Services\PartEvaluationServices\HasDuplicatedPackage;
use Bakgul\Evaluator\Tests\EvaluatorTestMethods;

class DuplicatedPackageTest extends EvaluatorTestMethods
{
    use HasTestMethods;
    
    private $evaluator;

    public function __construct()
    {
        $this->evaluator = HasDuplicatedPackage::class;

        parent::__construct();
    }

    /** @test */
    public function evaluator_will_return_null_when_the_command_has_a_new_package()
    {
        $this->assertNull($this->evaluator::handle(
            $this->setRequest(['package' => 'xxxx'], 'package'))
        );
    }

    /** @test */
    public function evaluator_will_return_error_object_when_the_command_has_a_package_that_already_exists()
    {
        $response = $this->evaluator::handle($this->setRequest(key: 'package'));

        $this->assertNotNull($response);
        $this->assertEquals($response['key'], 'package');
        $this->assertEquals($response['evaluated'], 'duplicated');
        $this->assertEquals($response['is_confirmable'], false);
        $this->assertTrue(str_contains($response['message'], "'{$this->testPackage['name']}' is already exist"));
    }
}
