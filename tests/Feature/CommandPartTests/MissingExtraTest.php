<?php

namespace Bakgul\Evaluator\Tests\Feature\CommandPartTests;

use Bakgul\Evaluator\Services\PartEvaluationServices\HasMissingExtra;
use Bakgul\Kernel\Tests\Concerns\HasTestMethods;
use Bakgul\Evaluator\Tests\EvaluatorTestMethods;
use Bakgul\Kernel\Tests\Tasks\SetupTest;

class MissingExtraTest extends EvaluatorTestMethods
{
    use HasTestMethods;

    private $evaluator;

    public function __construct()
    {
        $this->evaluator = HasMissingExtra::class;

        parent::__construct();
    }

    /** @test */
    public function evaluator_will_return_null_when_type_is_view_and_extra_is_provided_regardless_of_app()
    {
        $this->testPackage = (new SetupTest)();

        foreach (['admin', 'web', null] as $app) {
            $this->assertNull($this->evaluator::handle($this->setRequest([
                'type' => 'view:component:vue',
                'app' => $app
            ], 'resource')));
        }
    }

    /** @test */
    public function evaluator_will_return_null_when_type_is_not_view_and_command_has_no_app()
    {
        $this->testPackage = (new SetupTest)();

        foreach (['js:component:class', 'css:composite'] as $type) {
            $this->assertNull($this->evaluator::handle($this->setRequest([
                'type' => $type,
                'app' => null
            ], 'resource')));
        }
    }

    /** @test */
    public function evaluator_will_return_error_object_due_to_lack_of_specs_when_type_is_view_and_command_has_no_app()
    {
        $this->testPackage = (new SetupTest)();

        $response = $this->evaluator::handle($this->setRequest(['app' => null], 'resource'));

        $this->assertNotNull($response);

        $this->assertTrue($response['key'] == 'extra');
        $this->assertTrue($response['evaluated'] == 'missing');
        $this->assertTrue($response['is_confirmable'] == false);
        $this->assertTrue(str_contains($response['message'], 'view:component:blade'));
    }
}
