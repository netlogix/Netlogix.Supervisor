<?php
declare(strict_types=1);

namespace Netlogix\Supervisor\Tests\Functional\Model\Program;

use Netlogix\Supervisor\Model\Program;
use function assert;

class DirectoryTest extends ProgramTestCase
{
    /**
     * @test
     */
    public function Programs_default_to_FLOW_ROOT_PATH_as_directory(): void
    {
        $program = new Program(
            'name',
            'groupName',
            'command',
            []
        );

        assert(\defined('FLOW_PATH_ROOT'));
        self::assertEquals(\FLOW_PATH_ROOT, $program->getArguments()['directory']);
    }

    /**
     * @test
     */
    public function Programs_can_overwrite_their_directory(): void
    {
        $program = new Program(
            'name',
            'groupName',
            'command',
            [
                'directory' => '/some/other/directory'
            ]
        );

        self::assertEquals('/some/other/directory', $program->getArguments()['directory']);
    }
}
