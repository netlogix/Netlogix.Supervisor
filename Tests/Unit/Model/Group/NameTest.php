<?php

declare(strict_types=1);

namespace Netlogix\Supervisor\Tests\Unit\Model\Group;

use Netlogix\Supervisor\Model\Group;

class NameTest extends GroupTestCase
{
    /**
     * @test
     */
    public function Groups_have_names(): void
    {
        $group = new Group('the-group-name', 100, $this->program);
        self::assertEquals('the-group-name', $group->getName());
    }

    /**
     * @test
     */
    public function Group_names_are_cleaned_up(): void
    {
        $group = new Group(PHP_EOL . ':the.grOUp\name###', 100, $this->program);
        self::assertEquals('the-group-name', $group->getName());
    }
}
