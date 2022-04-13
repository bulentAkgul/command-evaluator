<?php

namespace Bakgul\Evaluator\Tests\Feature\CommandPartEvaluationTests;

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
            'name' => 'user:index,post:all',
            'type' => 'service'
        ])));
    }

    /** @test */
    public function evaluator_will_return_null_when_the_tasks_match_at_least_one_of_the_types()
    {
        $this->assertNull($this->evaluator::handle($this->setRequest([
            'name' => 'user:index,post:all,tag:store.destroy',
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
    public function evaluator_will_return_error_object_when_create_file_command_has_an_invalid_task_along_with_all()
    {
        $response = ($this->evaluator::handle($this->setRequest([
            'name' => 'user:all.xxx',
        ])));

        $this->assertNotNull($response);
        $this->assertEquals($response['key'], 'task');
        $this->assertEquals($response['evaluated'], 'unknown');
        $this->assertEquals($response['is_confirmable'], false);
        $this->assertTrue(str_contains($response['message'], 'Unknown or unmatched task: xxx'));
    }

    /** @test */
    public function evaluator_will_return_error_object_when_create_file_command_has_multiple_invalid_tasks()
    {
        $response = ($this->evaluator::handle($this->setRequest([
            'name' => 'user:all.xxx,post:yyy',
        ])));

        $this->assertNotNull($response);
        $this->assertEquals($response['key'], 'task');
        $this->assertEquals($response['evaluated'], 'unknown');
        $this->assertEquals($response['is_confirmable'], false);
        $this->assertTrue(str_contains($response['message'], 'Unknown or unmatched tasks: xxx, yyy'));
    }
}
