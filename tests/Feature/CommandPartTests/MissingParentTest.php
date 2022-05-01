<?php

namespace Bakgul\Evaluator\Tests\Feature\CommandPartTests;

use Bakgul\Kernel\Tests\Concerns\HasTestMethods;
use Bakgul\Evaluator\Services\PartEvaluationServices\HasMissingParent;
use Bakgul\Evaluator\Tests\EvaluatorTestMethods;

class MissingParentTest extends EvaluatorTestMethods
{
    use HasTestMethods;
    
    private $evaluator;

    public function __construct()
    {
        $this->evaluator = HasMissingParent::class;

        parent::__construct();
    }

    /** @test */
    public function evaluator_will_return_null_when_create_file_command_has_no_file_type_that_needs_a_parent()
    {
        $this->assertNull($this->evaluator::handle($this->setRequest()));
    }

    /** @test */
    public function evaluator_will_return_null_when_create_file_command_has_parent_even_if_there_is_no_file_that_needs_one()
    {
        $this->assertNull($this->evaluator::handle($this->setRequest(
            ['type' => 'model', 'parent' => 'posts']
        )));
    }

    /** @test */
    public function evaluator_will_return_error_object_when_the_create_file_command_has_a_file_type_that_needs_a_parent_but_command_has_no_parent()
    {
        $response = $this->evaluator::handle($this->setRequest(
            ['type' => 'controller:nested']
        ));

        $this->assertNotNull($response);

        $this->assertTrue($response['key'] == 'parent');
        $this->assertTrue($response['evaluated'] == 'missing');
        $this->assertTrue(str_contains($response['message'], 'controller:nested'));
    }
}
