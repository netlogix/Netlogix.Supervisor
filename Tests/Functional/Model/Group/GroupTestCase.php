<?php
declare(strict_types=1);

namespace Netlogix\Supervisor\Tests\Functional\Model\Group;

use Neos\Flow\Tests\FunctionalTestCase;
use Netlogix\Supervisor\Model\Program;

class GroupTestCase extends FunctionalTestCase
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
