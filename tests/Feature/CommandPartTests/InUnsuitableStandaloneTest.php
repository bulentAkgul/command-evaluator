<?php

namespace Bakgul\Evaluator\Tests\Feature\CommandPartTests;

use Bakgul\Kernel\Tests\Concerns\HasTestMethods;
use Bakgul\Evaluator\Services\PartEvaluationServices\RunsInUnsuitableStandalone;
use Bakgul\Evaluator\Tests\EvaluatorTestMethods;
use Bakgul\Kernel\Tests\Services\TestDataService;
use Bakgul\Kernel\Tests\Tasks\SetupTest;

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
        $this->testPackage = (new SetupTest)(TestDataService::standalone('pl'));
        
        $this->assertNull($this->evaluator::handle($this->setRequest(key: 'package')));
    }

    /** @test */
    public function evaluator_will_return_null_when_create_package_command_runs_in_standalone_package_first_time()
    {
        $this->testPackage = (new SetupTest)(TestDataService::standalone('sp'));

        $this->emptyBase();

        $this->assertNull($this->evaluator::handle($this->setRequest(key: 'package')));
    }

    /** @test */
    public function evaluator_will_return_error_when_create_package_command_runs_in_standalone_package_second_time()
    {
        $this->testPackage = (new SetupTest)(TestDataService::standalone('sp'));

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
        $this->testPackage = (new SetupTest)(TestDataService::standalone('sl'));
        
        $response = $this->evaluator::handle($this->setRequest(key: 'package'));

        $this->assertNotNull($response);

        $this->assertTrue($response['key'] == 'standalone');
        $this->assertTrue($response['evaluated'] == 'unsuitable');
        $this->assertTrue($response['is_confirmable'] == false);
        $this->assertTrue(str_contains($response['message'], 'A standalone Laravel app can\'t have a package.'));
    }
}
