<?php
declare(strict_types=1);

namespace Netlogix\Supervisor\Tests\Functional\Model\Program;

use Netlogix\Supervisor\Model\Program;
use function assert;

class NameTest extends ProgramTestCase
{
    /**
     * @test
     */
    public function Programs_have_names(): void
    {
        $program = new Program(
            'the-program-name',
            'groupName',
            'command',
            []
        );

        self::assertEquals('the-program-name', $program->getName());
    }

    /**
     * @test
     */
    public function Program_name_is_cleaned_up(): void
    {
        $program = new Program(
            PHP_EOL . 'the-proGRam/name###',
            'groupName',
            'command',
            []
        );

        self::assertEquals('the-program-name', $program->getName());
    }
}
