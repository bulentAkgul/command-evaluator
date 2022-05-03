<?php

namespace Bakgul\Evaluator\Tests\Feature\CommandPartTests;

use Bakgul\Evaluator\Services\PartEvaluationServices\HasExcessMediator;
use Bakgul\Kernel\Tests\Concerns\HasTestMethods;
use Bakgul\Evaluator\Tests\EvaluatorTestMethods;
use Bakgul\Kernel\Helpers\Settings;

class ExcessMediatorTest extends EvaluatorTestMethods
{
    use HasTestMethods;

    private $evaluator;

    public function __construct()
    {
        $this->evaluator = HasExcessMediator::class;

        parent::__construct();
    }

    /** @test */
    public function evaluator_will_return_null_when_relation_is_not_mtm_even_if_polymorphic_and_mediator_are_truety()
    {
        $this->assertNull($this->evaluator::handle($this->setRequest([
            'relation' => 'oto'
        ], 'relation'), []));

        $this->assertNull($this->evaluator::handle($this->setRequest([
            'relation' => 'otm',
            'polymorphic' => true,
            'mediator' => 'some_name'
        ], 'relation'), []));
    }

    /** @test */
    public function evaluator_will_return_null_unless_both_polymorphic_and_mediator_are_truety()
    {
        $this->assertNull($this->evaluator::handle($this->setRequest([
            'relation' => 'mtm',
            'polymorphic' => false,
            'mediator' => 'some_name'
        ], 'relation'), []));

        $this->assertNull($this->evaluator::handle($this->setRequest([
            'relation' => 'mtm',
            'polymorphic' => true,
            'mediator' => null
        ], 'relation'), []));
    }

    /** @test */
    public function evaluator_will_return_confirmation_object_when_relation_is_mtm_if_polymorphic_and_mediator_are_truty()
    {
        Settings::set('evaluator.disable_warnings_unless_a_new_value_can_be_provided', false);

        $response = $this->evaluator::handle($this->setRequest([
            'relation' => 'mtm',
            'mediator' => 'some_name',
            'polymorphic' => true
        ], 'relation'), []);

        $this->assertConfirmationObject($response, 'mtm');
    }

    private function assertConfirmationObject(array $response, string $relation)
    {
        $this->assertNotNull($response);
        $this->assertEquals($response['key'], 'relation');
        $this->assertEquals($response['evaluated'], 'excess');
        $this->assertEquals($response['is_confirmable'], true);
        $this->assertTrue(str_contains($response['message'], 'Many To Many Polymorphic'));
        $this->assertTrue(str_contains($response['message'], "Therefore 'some_name' will be ignored"));
    }
}
