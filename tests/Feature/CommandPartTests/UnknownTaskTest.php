<?php

namespace Bakgul\Evaluator\Tests\Feature\CommandPartTests;

use Bakgul\Kernel\Tests\Concerns\HasTestMethods;
use Bakgul\Evaluator\Services\PartEvaluationServices\HasUnknownTask;
use Bakgul\Evaluator\Tests\EvaluatorTestMethods;

class UnknownTaskTest extends EvaluatorTestMethods
{
    use HasTestMethods;
    
    private $evaluator;

    public function __construct()
    {
        $this->evaluator = HasUnknownTask::class;

        parent::__construct();
    }

    /** @test */
    public function evaluator_will_return_null_when_create_file_command_has_no_task()
    {
        $this->assertNull($this->evaluator::handle($this->setRequest()));
    }

    /** @test */
    public function evaluator_will_return_null_when_create_file_command_has_a_valid_task()
    {
        $this->assertNull($this->evaluator::handle($this->setRequest([
            'name' => 'user:index',
            'type' => 'service'
        ])));

        $this->assertNull($this->evaluator::handle($this->setRequest([
            'name' => 'user:store',
            'type' => 'request'
        ])));
    }

    /** @test */
    public function evaluator_will_return_null_when_the_tasks_match_at_least_one_of_the_types()
    {
        $this->assertNull($this->evaluator::handle($this->setRequest([
            'name' => 'user:index,post,tag:store.destroy',
            'type' => 'request,service'
        ])));
    }

    /** @test */
    public function evaluator_will_return_error_object_when_create_file_command_has_an_invalid_task()
    {
        $response = ($this->evaluator::handle($this->setRequest([
            'name' => 'user:xxx',
        ])));

        $this->assertNotNull($response);
        $this->assertEquals($response['key'], 'task');
        $this->assertEquals($response['evaluated'], 'unknown');
        $this->assertEquals($response['is_confirmable'], false);
        $this->assertTrue(str_contains($response['message'], 'Unknown or unmatched task'));
    }

    /** @test */
    public function evaluator_will_return_error_object_when_create_file_command_has_multiple_invalid_tasks()
    {
        $response = ($this->evaluator::handle($this->setRequest([
            'name' => 'user:xxx,post:yyy',
        ])));

        $this->assertNotNull($response);
        $this->assertEquals($response['key'], 'task');
        $this->assertEquals($response['evaluated'], 'unknown');
        $this->assertEquals($response['is_confirmable'], false);
        $this->assertTrue(str_contains($response['message'], 'Unknown or unmatched tasks: xxx, yyy'));
    }

    /** @test */
    public function evaluator_will_return_null_when_the_requested_tasks_are_belongs_to_at_least_one_of_file_types_in_the_queue()
    {
        $this->assertNull($this->evaluator::handle($this->setRequest([
            'name' => 'user:index,post,tag:store.destroy',
            'type' => 'controller'
        ])));
    }
}
