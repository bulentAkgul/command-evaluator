<?php

namespace Bakgul\Evaluator\Tests\Feature\CommandPartEvaluationTests;

use Bakgul\Evaluator\Tests\EvaluatorTestMethods;

class UnknownRoleTest extends EvaluatorTestMethods
{
    private $evaluator;

    public function __construct()
    {
        $this->evaluator = HasUnknownRole::class;

        parent::__construct();
    }

    /** @test */
    public function evaluator_will_return_null_when_create_file_command_has_no_role()
    {
        $this->assertTrue(true, 'will be tested while is being created laravel-resource-creator');
    }

    /** @test */
    public function evaluator_will_return_null_when_a_type_needs_a_role_but_it_has_not_been_attached_because_default_role_will_be_used()
    {
        $this->assertTrue(true, 'will be tested while is being created laravel-resource-creator');
    }

    /** @test */
    public function evaluator_will_return_error_object_when_an_unknown_task_is_attached()
    {
        $this->assertTrue(true, 'will be tested while is being created laravel-resource-creator');
    }

    /** @test */
    public function evaluator_will_return_error_object_when_multiple_unknown_tasks_are_attached()
    {
        $this->assertTrue(true, 'will be tested while is being created laravel-resource-creator');
    }
}
