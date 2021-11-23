<?php

declare(strict_types=1);

namespace Netlogix\Supervisor\Tests\Unit\Model\Group;

use Netlogix\Supervisor\Model\Program;
use Netlogix\Supervisor\Tests\Unit\SupervisorTestCase;

class GroupTestCase extends SupervisorTestCase
{
    /**
     * @var Program
     */
    protected $program;

    public function setUp(): void
    {
        parent::setUp();

        $this->program = new Program(
            'programName',
            'groupName',
            './flow help',
            []
        );
    }
}
