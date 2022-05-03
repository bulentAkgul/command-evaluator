<?php

namespace Bakgul\Evaluator\Tests\Feature\CommandPartTests;

use Bakgul\Kernel\Tests\Concerns\HasTestMethods;
use Bakgul\Evaluator\Services\PartEvaluationServices\HasUnknownRelation;
use Bakgul\Evaluator\Tests\EvaluatorTestMethods;

class UnknownRelationTest extends EvaluatorTestMethods
{
    use HasTestMethods;

    private $evaluator;

    public function __construct()
    {
        $this->evaluator = HasUnknownRelation::class;

        parent::__construct();
    }

    /** @test */
    public function evaluator_will_return_null_when_command_has_a_valid_relation_key()
    {
        $this->assertNull($this->evaluator::handle($this->setRequest(key: 'relation')));
    }

    /** @test */
    public function evaluator_will_return_null_when_command_has_a_valid_relation_name()
    {
        $this->assertNull($this->evaluator::handle(
            $this->setRequest(['relation' => 'one_to_many'], 'relation')
        ));
    }

    /** @test */
    public function evaluator_will_return_error_object_when_command_has_not_a_valid_relation()
    {
        $response = $this->evaluator::handle(
            $this->setRequest(['relation' => 'xxxx'], 'relation')
        );

        $this->assertNotNull($response);

        $this->assertTrue($response['key'] == 'relation');
        $this->assertTrue($response['evaluated'] == 'unknown');
        $this->assertTrue(str_contains($response['message'], 'unknown relation: xxxx'));
    }
}
