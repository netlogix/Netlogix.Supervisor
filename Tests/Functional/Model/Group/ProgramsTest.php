<?php
declare(strict_types=1);

namespace Netlogix\Supervisor\Tests\Functional\Model\Group;

use Netlogix\Supervisor\Exception\Group\EmptyGroupException;
use Netlogix\Supervisor\Model\Group;

class ProgramsTest extends GroupTestCase
{
    /**
     * @test
     */
    public function Groups_must_not_be_empty(): void
    {
        self::expectException(EmptyGroupException::class);
        self::expectExceptionCode(1634910620);
        new Group('name', 100);
    }

    /**
     * @test
     */
    public function Groups_have_programs(): void
    {
        $group = new Group('name', 100, $this->program, $this->program);
        self::assertEquals([$this->program, $this->program], $group->getPrograms());
    }

    /**
     * @test
     */
    public function Programs_can_be_addeed_to_groups(): void
    {
        $group = (new Group('name', 100, $this->program))
            ->withProgram($this->program);
        self::assertEquals([$this->program, $this->program], $group->getPrograms());
    }
}
