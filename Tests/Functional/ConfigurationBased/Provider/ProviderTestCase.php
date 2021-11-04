<?php
declare(strict_types=1);

namespace Netlogix\Supervisor\Tests\Functional\ConfigurationBased\Provider;

use Neos\Flow\Tests\FunctionalTestCase;

class ProviderTestCase extends FunctionalTestCase
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
