<?php
declare(strict_types=1);

namespace Netlogix\Supervisor\Tests\Functional\Renderer;

use Netlogix\Supervisor\Model\Program;
use Netlogix\Supervisor\Renderer\Renderer;
use Netlogix\Supervisor\Tests\Functional\SupervisorTestCase;

class GroupTest extends SupervisorTestCase
{
    /**
     * @test
     */
    public function Programs_can_be_rendered(): void
    {
        $program = new Program(
            'the-program-name',
            'the-group-name',
            './flow help',
            [
                'directory' => '/var/www/directory',
                'environment' => "FLOW_CONTEXT='Testing/Functional'",
                'true_value' => true,
                'false_value' => false,
                'zero_value' => 0,
                'integer_value' => 123456789,
            ]
        );

        $renderer = new Renderer();
        $rendered = $renderer->renderProgram($program);

        assert(defined('FLOW_PATH_ROOT'));
        $expected = <<<"INI"
[program:the-program-name]
directory=/var/www/directory
environment=FLOW_CONTEXT='Testing/Functional'
true_value=true
false_value=false
zero_value=0
integer_value=123456789
command=./flow help

INI;

        self::assertEquals($expected, $rendered);
    }

}
