<?php
declare(strict_types=1);

namespace Netlogix\Supervisor\Tests\Functional\Model\Program;

use Neos\Flow\Core\Bootstrap;
use Netlogix\Supervisor\Model\Program;
use function assert;

class EnvironmentTest extends ProgramTestCase
{
    /**
     * @test
     */
    public function Programs_default_to_FLOW_CONTEXT_for_environment_variables(): void
    {
        $program = new Program(
            'name',
            'groupName',
            'command',
            []
        );

        assert(\defined('FLOW_PATH_ROOT'));
        $flowContext = Bootstrap::getEnvironmentConfigurationSetting('FLOW_CONTEXT');
        self::assertEquals(sprintf("FLOW_CONTEXT='%s'", $flowContext), $program->getArguments()['environment']);
    }

    /**
     * @test
     */
    public function Programs_can_overwrite_their_environment_variables(): void
    {
        $program = new Program(
            'name',
            'groupName',
            'command',
            [
                'environment' => "FOO='bar'"
            ]
        );

        self::assertEquals("FOO='bar'", $program->getArguments()['environment']);
    }
}
