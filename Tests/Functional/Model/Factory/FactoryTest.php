<?php
declare(strict_types=1);

namespace Netlogix\Supervisor\Tests\Functional\Model\Factory;

use Netlogix\Supervisor\Model\Factory;
use Netlogix\Supervisor\Model\Program;
use Netlogix\Supervisor\Tests\Functional\SupervisorTestCase;

class FactoryTest extends SupervisorTestCase
{
    /**
     * @test
     */
    public function Factories_with_no_programs_are_empty(): void
    {
        $factory = new Factory();
        $groups = $factory->getGroups();
        self::assertEmpty($groups);
    }

    /**
     * @test
     */
    public function Factories_create_groups_by_programs(): void
    {
        $program = new Program(
            'programName',
            'default-group',
            './flow help'
        );

        $factory = new Factory();
        $factory->registerProgram($program);
        $groups = $factory->getGroups();

        self::assertCount(1, $groups);
        self::assertEquals('default-group', $groups[0]->getName());
        self::assertEquals([$program], $groups[0]->getPrograms());
    }

    /**
     * @test
     */
    public function Group_names_can_be_overwritten(): void
    {
        $program = new Program(
            'programName',
            'default',
            './flow help'
        );

        $factory = new Factory();
        $this->inject(
            $factory,
            'groupSettings',
            [
                'default' => [
                    'name' => 'the-default-group'
                ]
            ]
        );

        $factory->registerProgram($program);
        $groups = $factory->getGroups();

        self::assertEquals('the-default-group', $groups[0]->getName());
    }

    /**
     * @test
     */
    public function Group_default_to_priority_999(): void
    {
        $program = new Program(
            'programName',
            'default',
            './flow help'
        );

        $factory = new Factory();
        $this->inject(
            $factory,
            'groupSettings',
            []
        );

        $factory->registerProgram($program);
        $groups = $factory->getGroups();

        self::assertEquals(999, $groups[0]->getPriority());
    }

    /**
     * @test
     */
    public function Group_priorities_can_be_overwritten(): void
    {
        $program = new Program(
            'programName',
            'default',
            './flow help'
        );

        $factory = new Factory();
        $this->inject(
            $factory,
            'groupSettings',
            [
                'default' => [
                    'priority' => 1234
                ]
            ]
        );

        $factory->registerProgram($program);
        $groups = $factory->getGroups();

        self::assertEquals(1234, $groups[0]->getPriority());
    }

    public function Programs_can_be_assigend_to_multiple_groups(): void
    {
        $program_a = new Program(
            'firstProgramName',
            'first-group',
            './flow help'
        );
        $program_b = new Program(
            'secondProgramName',
            'second-group',
            './flow help'
        );

        $factory = new Factory();
        $factory->registerProgram($program_a);
        $factory->registerProgram($program_b);

        self::assertCount(2, $factory->getGroups());
        self::assertEquals('first-group', $factory->getGroups()[0]);
        self::assertEquals('second-group', $factory->getGroups()[1]);
    }
}
