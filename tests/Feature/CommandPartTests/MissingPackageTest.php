<?php

namespace Bakgul\Evaluator\Tests\Feature\CommandPartTests;

use Bakgul\Kernel\Tests\Concerns\HasTestMethods;
use Bakgul\Evaluator\Services\PartEvaluationServices\HasMissingPackage;
use Bakgul\Evaluator\Tests\EvaluatorTestMethods;
use Bakgul\Kernel\Helpers\Settings;
use Bakgul\Kernel\Tests\Services\TestDataService;
use Bakgul\Kernel\Tests\Tasks\SetupTest;

class MissingPackageTest extends EvaluatorTestMethods
{
    use HasTestMethods;

    private $evaluator;

    public function __construct()
    {
        $this->evaluator = HasMissingPackage::class;

        parent::__construct();
    }

    /** @test */
    public function evaluator_will_return_null_regardles_of_the_package_if_the_project_is_standalone()
    {
        Settings::set('apps.admin.folder', 'xxx');

        foreach (['sl', 'sp', 'conflict'] as $isAlone) {
            $this->testPackage = (new SetupTest)(TestDataService::standalone($isAlone));

            foreach (['file', 'relation'] as $command) {
                foreach ([null, $this->testPackage['name'], 'admin', 'xxx', 'unknown_name'] as $package) {
                    $this->assertNull($this->evaluator::handle($this->setRequest(['package' => $package], $command), []));
                }
            }
        }
    }

    /** @test */
    public function evaluator_will_return_null_when_create_file_command_has_a_valid_package()
    {
        $this->testPackage = (new SetupTest)(TestDataService::standalone('pl'));

        $this->assertNull($this->evaluator::handle($this->setRequest(), []));
    }

    /** @test */
    public function evaluator_will_return_confimation_object_when_create_file_command_has_no_package()
    {
        $this->testPackage = (new SetupTest)(TestDataService::standalone('pl'));

        $response = $this->evaluator::handle($this->setRequest(['package' => null]), []);

        $this->assertNotNull($response);
        $this->assertEquals($response['key'], 'package');
        $this->assertEquals($response['evaluated'], 'missing');
        $this->assertEquals($response['is_confirmable'], true);
        $this->assertTrue(str_contains($response['message'], "The command has no package name."));
        $this->assertTrue(str_contains($response['message'], "Proceed?"));
    }
}
