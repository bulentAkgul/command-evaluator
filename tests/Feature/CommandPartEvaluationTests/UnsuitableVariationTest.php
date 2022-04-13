<?php

namespace Bakgul\Evaluator\Tests\Feature\CommandPartEvaluationTests;

use Bakgul\Kernel\Tests\Concerns\HasTestMethods;
use Bakgul\Evaluator\Services\PartEvaluationServices\HasUnsuitableVariation;
use Bakgul\Evaluator\Tests\EvaluatorTestMethods;

class UnsuitableVariationTest extends EvaluatorTestMethods
{
    use HasTestMethods;

    private $evaluator;

    public function __construct()
    {
        $this->evaluator = HasUnsuitableVariation::class;

        parent::__construct();
    }

    /** @test */
    public function evaluator_will_return_null_when_create_file_command_has_not_any_variation()
    {
        $this->assertNull($this->evaluator::handle($this->setRequest([
            'type' => 'model'
        ])));
    }

    /** @test */
    public function evaluator_will_return_null_when_create_file_command_has_a_valid_variation()
    {
        $this->assertNull($this->evaluator::handle($this->setRequest([
            'type' => 'controller:api',
        ])));
    }

    /** @test */
    public function evaluator_will_return_error_object_when_create_file_command_has_an_unknown_variation()
    {
        $response = $this->evaluator::handle($this->setRequest([
            'type' => "controller:xxx,model:pivot"
        ]));

        $this->assertNotNull($response);
        $this->assertEquals($response['key'], 'variation');
        $this->assertEquals($response['evaluated'], 'unknown');
        $this->assertEquals($response['is_confirmable'], false);
        $this->assertTrue(str_contains($response['message'], 'Unknown or unmatched variation'));
    }
}
