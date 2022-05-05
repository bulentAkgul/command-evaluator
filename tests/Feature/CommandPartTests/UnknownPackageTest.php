<?php

namespace Bakgul\Evaluator\Tests\Feature\CommandPartTests;

use Bakgul\Kernel\Tests\Concerns\HasTestMethods;
use Bakgul\Evaluator\Services\PartEvaluationServices\HasUnknownPackage;
use Bakgul\Evaluator\Tests\EvaluatorTestMethods;
use Bakgul\Kernel\Helpers\Settings;
use Bakgul\Kernel\Tests\Services\TestDataService;
use Bakgul\Kernel\Tests\Tasks\SetupTest;

class UnknownPackageTest extends EvaluatorTestMethods
{
    use HasTestMethods;

    private $evaluator;

    public function __construct()
    {
        $this->evaluator = HasUnknownPackage::class;

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
    public function evaluator_will_return_confimation_object_when_create_file_command_has_no_or_an_unknown_package()
    {
        foreach ([['admin', null], ['unknown', null], ['unknown', 'admin']] as $case) {
            $response = $this->evaluator::handle($this->setRequest([
                'package' => $case[0],
                'app' => $case[1]
            ]), []);

            $this->assertNotNull($response);
            $this->assertEquals($response['key'], 'package');
            $this->assertEquals($response['evaluated'], 'unknown');
            $this->assertEquals($response['is_confirmable'], true);
            $this->assertTrue(str_contains($response['message'], "'{$case[0]}'"));
        }
    }

    /** @test */
    public function evaluator_will_return_null_when_relation_creator_command_has_no_package()
    {
        $this->assertNull($this->evaluator::handle($this->setRequest(key: 'relation'), []));
    }

    /** @test */
    public function evaluator_will_return_null_when_relation_creator_command_has_valid_packages()
    {
        $this->testPackage = (new SetupTest)(TestDataService::standalone('pl'));

        $this->assertNull($this->evaluator::handle($this->setRequest([
            'from' => 'testing/user',
            'to' => 'testing/post',
            'pivot' => 'users/post_user',
            'package' => 'testing,users'
        ], 'relation'), []));
    }

    /** @test */
    public function evaluator_will_return_error_when_relation_creator_command_has_an_unknown_packages()
    {
        $response = $this->evaluator::handle($this->setRequest([
            'from' => 'xxx/user',
            'to' => 'post',
            'pivot' => 'users/post_user',
            'package' => 'xxx,users'
        ], 'relation'), []);

        $this->assertNotNull($response);
        $this->assertEquals($response['key'], 'package');
        $this->assertEquals($response['evaluated'], 'unknown');
        $this->assertEquals($response['is_confirmable'], false);
        $this->assertTrue(str_contains($response['message'], "The command has been terminated"));
    }
}
