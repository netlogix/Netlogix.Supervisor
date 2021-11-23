<?php

declare(strict_types=1);

namespace Netlogix\Supervisor\Tests\Unit\Model\Group;

use Netlogix\Supervisor\Model\Group;

class PriorityTest extends GroupTestCase
{
    /**
     * @test
     */
    public function Groups_have_a_priority(): void
    {
        $group = new Group('the-group-name', 100, $this->program);
        self::assertEquals(100, $group->getPriority());
    }

    /**
     * @test
     */
    public function Group_expose_their_priority_as_argument(): void
    {
        $group = new Group('the-group-name', 100, $this->program);
        self::assertEquals(
            ['priority' => 100],
            $group->getArguments()
        );
    }
}
