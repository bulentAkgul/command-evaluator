<?php

namespace Bakgul\Evaluator\Tests\Feature\CommandPartTests;

use Bakgul\Kernel\Tests\Concerns\HasTestMethods;
use Bakgul\Evaluator\Services\PartEvaluationServices\HasUnknownType;
use Bakgul\Evaluator\Tests\EvaluatorTestMethods;

class UnknownTypeTest extends EvaluatorTestMethods
{
    use HasTestMethods;

    private $evaluator;

    public function __construct()
    {
        $this->evaluator = HasUnknownType::class;

        parent::__construct();
    }

    /** @test */
    public function evaluator_will_return_null_when_create_file_command_has_a_valid_type()
    {
        $this->assertNull($this->evaluator::handle($this->setRequest()));
    }

    /** @test */
    public function evaluator_will_return_error_object_when_create_file_command_has_invalid_types()
    {
        foreach (['xx', 'xx,model', 'xxx,yyy'] as $type) {
            $response = $this->evaluator::handle($this->setRequest([
                'type' => $type
            ]));

            $this->assertNotNull($response);
            $this->assertEquals($response['key'], 'type');
            $this->assertEquals($response['evaluated'], 'unknown');
            $this->assertEquals($response['is_confirmable'], false);
            $this->assertTrue(str_contains($response['message'], 'Unknown or unmatched type'));
        }
    }
}
