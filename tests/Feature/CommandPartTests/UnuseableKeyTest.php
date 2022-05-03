<?php

namespace Bakgul\Evaluator\Tests\Feature\CommandPartTests;

use Bakgul\Evaluator\Services\PartEvaluationServices\HasUnuseableKey;
use Bakgul\Kernel\Tests\Concerns\HasTestMethods;
use Bakgul\Evaluator\Tests\EvaluatorTestMethods;
use Bakgul\Kernel\Helpers\Settings;

class UnuseableKeyTest extends EvaluatorTestMethods
{
    use HasTestMethods;

    private $evaluator;

    public function __construct()
    {
        $this->evaluator = HasUnuseableKey::class;

        parent::__construct();
    }

    /** @test */
    public function evaluator_will_return_null_when_relation_is_not_mtm_even_if_polymorphic_are_true_and_custom_columns_are_provided()
    {
        $this->assertNull($this->evaluator::handle($this->setRequest([
            'relation' => 'oto'
        ], 'relation'), []));

        $this->assertNull($this->evaluator::handle($this->setRequest([
            'relation' => 'otm',
            'polymorphic' => true,
            'from' => 'post:custom_id'
        ], 'relation'), []));
    }

    /** @test */
    public function evaluator_will_return_null_unless_polymorphic_is_false_or_keys_are_default()
    {
        $this->assertNull($this->evaluator::handle($this->setRequest([
            'relation' => 'mtm',
            'polymorphic' => true,
        ], 'relation'), []));

        $this->assertNull($this->evaluator::handle($this->setRequest([
            'relation' => 'mtm',
            'polymorphic' => false,
            'from' => 'user:custom_id'
        ], 'relation'), []));
    }

    /** @test */
    public function evaluator_will_return_confirmation_object_when_relation_is_mtm_if_polymorphic_is_true_and_custom_column_is_provided()
    {
        Settings::set('evaluator.disable_warnings', false);

        $response = $this->evaluator::handle($this->setRequest([
            'relation' => 'mtm',
            'polymorphic' => true,
            'from' => 'post:custom_id',
        ], 'relation'), []);

        $this->assertConfirmationObject($response, 'mtm');

        $response = $this->evaluator::handle($this->setRequest([
            'relation' => 'mtm',
            'polymorphic' => true,
            'to' => 'post:custom_id',
        ], 'relation'), []);

        $this->assertConfirmationObject($response, 'mtm');

        $response = $this->evaluator::handle($this->setRequest([
            'relation' => 'mtm',
            'polymorphic' => true,
            'from' => 'user:random_id',
            'to' => 'post:custom_id',
        ], 'relation'), []);

        $this->assertConfirmationObject($response, 'mtm');
    }

    private function assertConfirmationObject(array $response)
    {
        $this->assertNotNull($response);
        $this->assertEquals($response['key'], 'relation');
        $this->assertEquals($response['evaluated'], 'unusable');
        $this->assertEquals($response['is_confirmable'], true);
        $this->assertTrue(str_contains($response['message'], 'Many To Many Polymorphic'));
        $this->assertTrue(str_contains($response['message'], "the given column names will be ignored"));
    }
}
