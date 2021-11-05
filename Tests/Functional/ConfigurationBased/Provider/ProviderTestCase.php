<?php
declare(strict_types=1);

namespace Netlogix\Supervisor\Tests\Functional\ConfigurationBased\Provider;

use Netlogix\Supervisor\Tests\Functional\SupervisorTestCase;

class ProviderTestCase extends SupervisorTestCase
{
    const PROGRAM_NAME = 'program-identifier';

    const PROGRAMS_FIXTURE = [
        self::PROGRAM_NAME => [
            'name' => 'program-name',
            'command' => './flow help',
            'groupName' => 'default'
        ],
    ];
}
