<?php

namespace Bakgul\Evaluator\Tests\Feature\CommandPartTests;

use Bakgul\Kernel\Helpers\Path;
use Bakgul\Kernel\Tests\Concerns\HasTestMethods;
use Bakgul\Kernel\Helpers\Settings;
use Bakgul\Kernel\Tests\Tasks\SetupTest;
use Bakgul\Evaluator\Services\PartEvaluationServices\HasUnknownModel;
use Bakgul\Evaluator\Tests\EvaluatorTestMethods;
use Bakgul\Kernel\Tests\Services\TestDataService;

class UnknownModelTest extends EvaluatorTestMethods
{
    use HasTestMethods;

    private $evaluator;

    public function __construct()
    {
        $this->evaluator = HasUnknownModel::class;

        parent::__construct();
    }

    /** @test */
    public function evaluator_will_return_null_when_create_relation_command_has_valid_models_in_one_package()
    {
        $this->createModels([[$this->package('pack1'), 'User'], [$this->package('pack1'), 'Post']]);

        $this->assertNull($this->evaluator::handle($this->setRequest(key: 'relation')));
    }

    /** @test */
    public function evaluator_will_return_null_when_create_relation_command_has_valid_models_in_specified_single_package()
    {
        $this->createModels([[$this->package('pack1'), 'User'], [$this->package('pack1'), 'Post']]);

        $this->assertNull($this->evaluator::handle($this->setRequest([
            'from' => 'pack1/user:u_id',
            'to' => 'pack1/post:p_id'
        ], 'relation')));
    }

    /** @test */
    public function evaluator_will_return_null_when_create_relation_command_has_valid_models_in_specified_multiple_packages()
    {
        $this->createModels([[$this->package('pack1'), 'User'], [$this->package('pack2'), 'Post']]);

        $this->assertNull($this->evaluator::handle($this->setRequest([
            'from' => 'pack1/user:u_id',
            'to' => 'pack2/post:p_id'
        ], 'relation')));
    }

    /** @test */
    public function evaluator_will_return_null_when_create_relation_command_has_valid_models_in_specified_package_and_app()
    {
        $this->createModels([[$this->package('pack1'), 'User'], ['app', 'Post']]);

        $this->assertNull($this->evaluator::handle($this->setRequest([
            'from' => 'pack1/user',
            'to' => 'post'
        ], 'relation')));
    }

    /** @test */
    public function evaluator_will_return_null_when_create_relation_command_has_valid_models_in_app()
    {
        $this->createModels([['app', 'User'], ['app', 'Post']]);

        $this->assertNull($this->evaluator::handle($this->setRequest([
            'from' => 'user',
            'to' => 'post'
        ], 'relation')));
    }

    /** @test */
    public function evaluator_will_return_error_object_when_create_relation_command_has_valid_models_in_wrong_packages()
    {
        $this->createModels([[$this->package('pack1'), 'User'], [$this->package('pack2'), 'Post']]);

        $response = $this->evaluator::handle($this->setRequest([
            'from' => 'pack2/user',
            'to' => 'post'
        ], 'relation'));

        $this->assertNotNull($response);

        $this->assertTrue($response['key'] == 'model');
        $this->assertTrue($response['evaluated'] == 'unknown');
        $this->assertTrue(str_contains($response['message'], 'model: pack2/User'));
    }

    /** @test */
    public function evaluator_will_return_error_object_when_create_relation_command_has_an_invalid_model()
    {
        $this->createModels([[$this->package('pack1'), 'Tag'], [$this->package('pack2'), 'Post']]);

        $response = $this->evaluator::handle($this->setRequest([
            'from' => 'pack1/user',
            'to' => 'post'
        ], 'relation'));

        $this->assertNotNull($response);

        $this->assertTrue($response['key'] == 'model');
        $this->assertTrue($response['evaluated'] == 'unknown');
        $this->assertTrue(str_contains($response['message'], 'model: pack1/User'));
    }

    /** @test */
    public function evaluator_will_return_null_when_create_relation_command_has_valid_models_in_standalone_app_by_ignoring_packages()
    {
        foreach (['sl', 'sp', 'conflict'] as $isAlone) {
            $this->testPackage = (new SetupTest)(TestDataService::standalone($isAlone));
            
            $this->createModels([['src', 'User'], ['src', 'Post']]);
            
            $this->assertNull($this->evaluator::handle($this->setRequest([
                'from' => 'pack1/user',
                'to' => 'pack2/post'
            ], 'relation')));
        }
    }

    /** @test */
    public function evaluator_will_return_error_object_when_create_relation_command_has_two_invalid_models()
    {
        $this->createModels([[$this->package('pack1'), 'Tag'], [$this->package('pack2'), 'Category']]);

        $response = $this->evaluator::handle($this->setRequest([
            'from' => 'pack1/user',
            'to' => 'post'
        ], 'relation'));

        $this->assertNotNull($response);

        $this->assertTrue($response['key'] == 'model');
        $this->assertTrue($response['evaluated'] == 'unknown');
        $this->assertTrue(str_contains($response['message'], 'models: pack1/User, Post'));
    }

    private function package(string $name)
    {
        return Path::glue([Settings::main('packages_root'), "core", $name, "src"]);
    }
}
