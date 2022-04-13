<?php

namespace Bakgul\Evaluator\Tests\Feature\CommandPartEvaluationTests;

use Bakgul\Kernel\Tests\Concerns\HasTestMethods;
use Bakgul\Evaluator\Services\PartEvaluationServices\HasConflictingParent;
use Bakgul\Evaluator\Tests\EvaluatorTestMethods;

class ConflictingParentTest extends EvaluatorTestMethods
{
    use HasTestMethods;
    
    public $evaluator;

    public function __construct()
    {
        $this->evaluator = HasConflictingParent::class;

        parent::__construct();
    }

    /** @test */
    public function evaluator_will_return_null_when_create_file_command_has_no_variation_at_all()
    {
        $this->assertNull($this->evaluator::handle($this->setRequest()));
    }

    /** @test */
    public function evaluator_will_return_null_when_create_file_command_has_no_variation_that_needs_a_parent()
    {
        $this->assertNull($this->evaluator::handle($this->setRequest([
            'type' => 'controller:api'
        ])));
    }

    /** @test */
    public function evaluator_will_return_null_when_create_file_command_has_one_variation_that_needs_a_parent()
    {
        $this->assertNull($this->evaluator::handle($this->setRequest([
            'type' => 'controller:nested-api'
        ])));
    }

    /** @test */
    public function evaluator_will_return_error_object_when_create_file_command_has_multiple_types_that_need_parents()
    {
        config()->set('packagify.main.need_parent.pivot', 'factory');
        
        $response = $this->evaluator::handle($this->setRequest([
            'type' => 'controller:nested-api,model:pivot'
        ]));

        $this->assertNotNull($response);

        $this->assertTrue($response['key'] == 'parent');
        $this->assertTrue($response['evaluated'] == 'conflicting');
        $this->assertTrue(str_contains($response['message'], '(controller:nested-api, model:pivot)'));
    }
}
