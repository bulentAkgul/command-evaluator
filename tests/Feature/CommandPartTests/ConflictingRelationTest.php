<?php

namespace Bakgul\Evaluator\Tests\Feature\CommandPartTests;

use Bakgul\Evaluator\Services\PartEvaluationServices\HasConflictingRelation;
use Bakgul\Kernel\Tests\Concerns\HasTestMethods;
use Bakgul\Evaluator\Tests\EvaluatorTestMethods;
use Bakgul\Kernel\Helpers\Settings;

class ConflictingRelationTest extends EvaluatorTestMethods
{
    use HasTestMethods;

    private $evaluator;

    public function __construct()
    {
        $this->evaluator = HasConflictingRelation::class;

        parent::__construct();
    }

    /** @test */
    public function evaluator_will_return_null_when_relation_is_mtm_even_if_polymorphic_and_mediator_are_truety()
    {
        $this->assertNull($this->evaluator::handle($this->setRequest([
            'relation' => 'mtm'
        ], 'relation'), []));

        $this->assertNull($this->evaluator::handle($this->setRequest([
            'relation' => 'mtm',
            'polymorphic' => true,
            'mediator' => 'some_name'
        ], 'relation'), []));
    }

    /** @test */
    public function evaluator_will_return_null_unless_both_polymorphic_and_mediator_are_truety()
    {
        $this->assertNull($this->evaluator::handle($this->setRequest([
            'polymorphic' => false,
            'mediator' => 'some_name'
        ], 'relation'), []));

        $this->assertNull($this->evaluator::handle($this->setRequest([
            'polymorphic' => true,
            'mediator' => null
        ], 'relation'), []));
    }

    /** @test */
    public function evaluator_will_return_confirmation_object_when_relation_is_not_mtm_if_polymorphic_and_mediator_are_truty()
    {
        Settings::set('evaluator.disable_warnings', false);

        foreach (['oto', 'otm'] as $relation) {
            $response = $this->evaluator::handle($this->setRequest([
                'relation' => $relation,
                'mediator' => 'some_name',
                'polymorphic' => true
            ], 'relation'), []);

            $this->assertConfirmationObject($response, $relation);
        }
    }

    private function assertConfirmationObject(array $response, string $relation)
    {
        $this->assertNotNull($response);
        $this->assertEquals($response['key'], 'relation');
        $this->assertEquals($response['evaluated'], 'conflicting');
        $this->assertEquals($response['is_confirmable'], true);
        $this->assertTrue(str_contains($response['message'], 'Polymorphic Has ' . ($relation == 'oto' ? 'One' : 'Many') . ' Through'));
        $this->assertTrue(str_contains($response['message'], 'One to ' . ($relation == 'oto' ? 'One' : 'Many') . ' Polymorphic'));
    }
}
