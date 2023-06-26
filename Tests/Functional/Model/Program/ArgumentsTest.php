<?php
declare(strict_types=1);

namespace Netlogix\Supervisor\Tests\Functional\Model\Program;

use Netlogix\Supervisor\Model\Program;
use Traversable;
use function assert;

class ArgumentsTest extends ProgramTestCase
{
    /**
     * @test
     */
    public function Programs_hold_arguments(): void
    {
        assert(\defined('FLOW_PATH_ROOT'));
        $command = 'command';
        $directory = \FLOW_PATH_ROOT;
        $environment = 'FOO=bar';
        $arguments = [
            'command' => $command,
            'directory' => $directory,
            'environment' => $environment,
            'some-other-argument' => 'some-argument-value',
        ];
        $program = new Program(
            'the-program-name',
            'groupName',
            $command,
            $arguments
        );

        self::assertEquals($arguments, $program->getArguments());
    }

    /**
     * @test
     * @dataProvider provideEmptyButNotNullArguments
     * @param mixed $emptyValue
     */
    public function Arguments_can_be_empty($emptyValue): void
    {
        $arguments = [
            'empty-argument' => $emptyValue
        ];
        $program = new Program(
            PHP_EOL . 'the-program/name###',
            'groupName',
            'command',
            $arguments
        );

        self::assertEquals($emptyValue, $program->getArguments()['empty-argument']);
    }

    /**
     * @return Traversable<string, array<false|0|''>>
     */
    public static function provideEmptyButNotNullArguments(): Traversable
    {
        yield 'false' => [false];
        yield 'zero' => [0];
        yield 'empty string' => [''];
    }

    /**
     * @test
     */
    public function Null_arguments_are_filtered(): void
    {
        $arguments = [
            'empty-argument' => null
        ];
        $program = new Program(
            PHP_EOL . 'the-program/name###',
            'groupName',
            'command',
            $arguments
        );

        self::assertArrayNotHasKey('empty-argument', $program->getArguments());
    }
}
