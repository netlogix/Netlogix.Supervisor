<?php
declare(strict_types=1);

namespace Netlogix\Supervisor\Tests\Functional\ConfigurationBased\Provider;

use Netlogix\Supervisor\ConfigurationBased\Provider;
use Netlogix\Supervisor\Model\Program;

class ProgramGroupTest extends ProviderTestCase
{
    /**
     * @test
     */
    public function A_program_can_be_assigned_to_a_group(): void
    {
        $fixture = self::PROGRAMS_FIXTURE;
        $fixture[self::PROGRAM_NAME]['groupName'] = 'some-other-group';

        $provider = new Provider();
        $this->inject($provider, 'programTemplates', $fixture);

        $program = current($provider->getPrograms());
        assert($program instanceof Program);

        self::assertEquals('some-other-group', $program->getGroupName());
    }

    /**
     * @test
     */
    public function A_program_with_no_assigned_group_is_in_the_default_group(): void
    {
        $fixture = self::PROGRAMS_FIXTURE;
        unset($fixture[self::PROGRAM_NAME]['groupName']);

        $provider = new Provider();
        $this->inject($provider, 'programTemplates', $fixture);

        $program = current($provider->getPrograms());
        assert($program instanceof Program);

        self::assertEquals('default', $program->getGroupName());
    }
}
