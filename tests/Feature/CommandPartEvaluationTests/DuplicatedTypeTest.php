<?php

namespace Bakgul\Evaluator\Tests\Feature\CommandPartEvaluationTests;

use Bakgul\Kernel\Tests\Concerns\HasTestMethods;
use Bakgul\Evaluator\Services\PartEvaluationServices\HasDuplicatedType;
use Bakgul\Evaluator\Tests\EvaluatorTestMethods;

class DuplicatedTypeTest extends EvaluatorTestMethods
{
    use HasTestMethods;
    
    private $evaluator;

    public function __construct()
    {
        $this->evaluator = HasDuplicatedType::class;

        parent::__construct();
    }

    /** @test */
    public function evaluator_will_return_null_when_command_has_single_type()
    {
        $this->assertNull($this->evaluator::handle($this->setRequest()));
    }

    /** @test */
    public function evaluator_will_return_null_when_command_has_unique_types()
    {
        $this->assertNull($this->evaluator::handle($this->setRequest([
            'type' => 'model,controller'
        ])));
    }

    /** @test */
    public function evaluator_will_return_error_object_when_the_command_has_the_same_file_type_repeatedly()
    {
        $response = $this->evaluator::handle($this->setRequest([
            'type' => 'controller:api,controller:nested-api,observer'
        ]));

        $this->assertNotNull($response);
        $this->assertEquals($response['key'], 'type');
        $this->assertEquals($response['evaluated'], 'duplicated');
        $this->assertEquals($response['is_confirmable'], false);
        $this->assertTrue(str_contains($response['message'], 'Each file type in a single command must be unique'));
    }
}
