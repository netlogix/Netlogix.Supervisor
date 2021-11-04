<?php
declare(strict_types=1);

namespace Netlogix\Supervisor\Tests\Functional\ConfigurationBased\Provider;

use Netlogix\Supervisor\ConfigurationBased\Provider;
use Netlogix\Supervisor\Exception\Program\EmptyNameException;
use Netlogix\Supervisor\Model\Program;

class ConvertSettingsToProgramsTest extends ProviderTestCase
{
    /**
     * @test
     */
    public function A_program_configuration_is_converted_to_a_program(): void
    {
        $fixture = self::PROGRAMS_FIXTURE;

        $provider = new Provider();
        $this->inject($provider, 'programTemplates', $fixture);

        $programs = $provider->getPrograms();
        self::assertCount(1, $programs);
        self::assertInstanceOf(Program::class, $programs[0]);
    }

    /**
     * @test
     */
    public function Two_program_configurations_are_converted_to_a_program(): void
    {
        $fixture = self::PROGRAMS_FIXTURE;
        $fixture['second-program'] = $fixture[self::PROGRAM_NAME];

        $provider = new Provider();
        $this->inject($provider, 'programTemplates', $fixture);

        $programs = $provider->getPrograms();
        self::assertCount(2, $programs);
        self::assertInstanceOf(Program::class, $programs[0]);
        self::assertInstanceOf(Program::class, $programs[1]);
    }
}
