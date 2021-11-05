<?php
declare(strict_types=1);

namespace Netlogix\Supervisor\Tests\Functional\Model\Group;

use Netlogix\Supervisor\Model\Program;
use Netlogix\Supervisor\Tests\Functional\SupervisorTestCase;

class GroupTestCase extends SupervisorTestCase
{
    /**
     * @var Program
     */
    protected $program;

    public function setUp()
    {
        $this->program = new Program(
            'programName',
            'groupName',
            './flow help',
            []
        );
    }
}
