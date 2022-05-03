<?php

namespace Bakgul\Evaluator\Tests\Feature\CommandPartTests;

use Bakgul\Kernel\Tests\Concerns\HasTestMethods;
use Bakgul\Evaluator\Services\PartEvaluationServices\HasMissingApp;
use Bakgul\Evaluator\Tests\EvaluatorTestMethods;

class MissingAppTest extends EvaluatorTestMethods
{
    use HasTestMethods;

    private $evaluator;

    public function __construct()
    {
        $this->evaluator = HasMissingApp::class;

        parent::__construct();
    }

    /** @test */
    public function evaluator_will_return_null_when_create_file_command_has_a_valid_or_invalid_app()
    {
        foreach (['admin', 'unkown'] as $app) {
            $this->assertNull($this->evaluator::handle($this->setRequest(
                ['app' => $app]
            ), []));
        }
    }

    /** @test */
    public function evaluator_will_return_null_when_create_file_command_has_no_app_and_no_type_that_needs_app()
    {
        $this->assertNull($this->evaluator::handle($this->setRequest(
            ['app' => null, 'type' => 'cast']
        ), []));
    }

    /** @test */
    public function evaluator_will_return_confirmation_object_when_command_has_a_type_that_needs_app_but_app_is_missing()
    {
        $response = $this->evaluator::handle($this->setRequest(
            ['app' => null, 'type' => 'controller']
        ), []);

        $this->assertConfirmationObject($response);
    }

    /** @test */
    public function evaluator_will_return_confirmation_object_when_one_of_types_has_a_related_type_that_needs_app_but_app_is_missing_and_associated_option_is_not_disabled()
    {
        config()->set('packagify.evaluator.disable_warnings', false);
        config()->set('packagify.files.cast.pairs', ['controller']);

        $response = $this->evaluator::handle($this->setRequest(
            ['app' => null, 'type' => 'cast']
        ), []);

        $this->assertConfirmationObject($response);

        config()->set('packagify.files.cast.pairs', []);
        config()->set('packagify.files.cast.require', ['controller', 'category', '']);

        $response = $this->evaluator::handle($this->setRequest(
            ['app' => null, 'type' => 'cast']
        ), []);

        $this->assertConfirmationObject($response);

        config()->offsetUnset('packagify.files.cast.require');
        config()->set('packagify.main.need_parent.cast', 'controller');

        $response = $this->evaluator::handle($this->setRequest(
            ['app' => null, 'type' => 'cast']
        ), []);

        $this->assertConfirmationObject($response);
    }

    /** @test */
    public function evaluator_will_return_null_when_one_of_types_has_a_related_type_that_needs_app_but_app_is_missing_and_associated_option_is_disabled()
    {
        config()->set('packagify.evaluator.disable_warnings', true);
        config()->set('packagify.files.cast.pairs', ['controller']);

        $this->assertNull($this->evaluator::handle($this->setRequest(
            ['app' => null, 'type' => 'cast']
        ), []));
    }

    private function assertConfirmationObject(array $response)
    {
        $this->assertNotNull($response);
        $this->assertEquals($response['key'], 'app');
        $this->assertEquals($response['evaluated'], 'missing');
        $this->assertEquals($response['is_confirmable'], true);
        $this->assertTrue(str_contains($response['message'], 'The command has no app name.'));
    }
}
