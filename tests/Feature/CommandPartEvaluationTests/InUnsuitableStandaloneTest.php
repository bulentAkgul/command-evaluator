<?php

namespace Bakgul\Evaluator\Tests\Feature\CommandPartEvaluationTests;

use Bakgul\Kernel\Tests\Concerns\HasTestMethods;
use Bakgul\Evaluator\Services\PartEvaluationServices\RunsInUnsuitableStandalone;
use Bakgul\Evaluator\Tests\EvaluatorTestMethods;

class InUnsuitableStandaloneTest extends EvaluatorTestMethods
{
    use HasTestMethods;
    
    private $evaluator;

    public function __construct()
    {
        $this->evaluator = RunsInUnsuitableStandalone::class;

        parent::__construct();
    }

    /** @test */
    public function evaluator_will_return_null_when_create_package_command_runs_in_packagified_laravel()
    {
        $this->standalone([false, false]);

        $this->assertNull($this->evaluator::handle($this->setRequest(key: 'package')));
    }

    /** @test */
    public function evaluator_will_return_null_when_create_package_command_runs_in_standalone_package_first_time()
    {
        $this->standalone([true, false]);

        $this->emptyBase();

        $this->assertNull($this->evaluator::handle($this->setRequest(key: 'package')));
    }

    /** @test */
    public function evaluator_will_return_error_when_create_package_command_runs_in_standalone_package_second_time()
    {
        $this->standalone([true, false]);

        $this->makeFakePackage();

        $response = $this->evaluator::handle($this->setRequest(key: 'package'));

        $this->assertNotNull($response);

        $this->assertTrue($response['key'] == 'standalone');
        $this->assertTrue($response['evaluated'] == 'unsuitable');
        $this->assertTrue($response['is_confirmable'] == false);
        $this->assertTrue(str_contains($response['message'], 'A standalone package can have only one package'));
    }

    /** @test */
    public function evaluator_will_return_error_when_create_package_command_runs_in_standalone_laravel()
    {
        $this->standalone([false, true]);

        $response = $this->evaluator::handle($this->setRequest(key: 'package'));

        $this->assertNotNull($response);

        $this->assertTrue($response['key'] == 'standalone');
        $this->assertTrue($response['evaluated'] == 'unsuitable');
        $this->assertTrue($response['is_confirmable'] == false);
        $this->assertTrue(str_contains($response['message'], 'A standalone Laravel app can\'t have a package.'));
    }
}
