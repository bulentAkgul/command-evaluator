<?php

namespace Bakgul\Evaluator\Tests\Feature\CommandPartTests;

use Bakgul\Kernel\Tests\Concerns\HasTestMethods;
use Bakgul\Evaluator\Services\PartEvaluationServices\HasUnknownApp;
use Bakgul\Evaluator\Tests\EvaluatorTestMethods;
use Bakgul\Kernel\Helpers\Settings;
use Illuminate\Support\Facades\Config;

class UnknownAppTest extends EvaluatorTestMethods
{
    use HasTestMethods;

    private $evaluator;

    public function __construct()
    {
        $this->evaluator = HasUnknownApp::class;

        parent::__construct();
    }

    /** @test */
    public function evaluator_will_return_null_when_create_file_command_has_a_valid_app_key_or_folder()
    {
        Config::set('packagify.apps.web.folder', 'xxx');

        foreach (['admin', 'xxx'] as $app) {
            $this->assertNull($this->evaluator::handle($this->setRequest(
                ['app' => $app]
            ), []));
        }
    }

    /** @test */
    public function evaluator_will_return_confirmation_object_when_command_has_unknown_app()
    {
        $response = $this->evaluator::handle($this->setRequest(
            ['app' => 'unknown']
        ), []);

        $this->assertNotNull($response);
        $this->assertEquals($response['key'], 'app');
        $this->assertEquals($response['evaluated'], 'unknown');
        $this->assertEquals($response['is_confirmable'], true);
        $this->assertTrue(str_contains($response['message'], "There is no app named 'unknown.'"));
    }

    /** @test */
    public function evaluator_will_return_null_when_warnings_are_disabled_even_if_command_has_unknown_app()
    {
        Settings::set('evaluator.disable_warnings', true);

        $this->assertNull($this->evaluator::handle($this->setRequest(
            ['app' => 'unknown']
        ), []));
    }
}
