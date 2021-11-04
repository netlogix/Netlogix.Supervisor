<?php
declare(strict_types=1);

namespace Netlogix\Supervisor\Tests\Functional\ConfigurationBased\Provider;

use Netlogix\Supervisor\ConfigurationBased\Provider;
use Netlogix\Supervisor\Exception\Program\EmptyNameException;

class ProgramNameTest extends ProviderTestCase
{
    /**
     * @test
     */
    public function Program_is_invalid_without_a_name(): void
    {
        $provider = new Provider();

        $fixture = self::PROGRAMS_FIXTURE;
        unset($fixture[self::PROGRAM_NAME]['name']);

        $this->inject($provider, 'programTemplates', $fixture);

        self::expectException(EmptyNameException::class);
        self::expectExceptionCode(1635175957);

        $provider->getPrograms();
    }

    /**
     * @test
     */
    public function Program_is_invalid_with_an_empty_name(): void
    {
        $provider = new Provider();

        $fixture = self::PROGRAMS_FIXTURE;
        $fixture[self::PROGRAM_NAME]['name'] = '';

        $this->inject($provider, 'programTemplates', $fixture);

        self::expectException(EmptyNameException::class);
        self::expectExceptionCode(1635175957);

        $provider->getPrograms();
    }

    /**
     * @test
     */
    public function Program_is_invalid_with_null_as_name(): void
    {
        $provider = new Provider();

        $fixture = self::PROGRAMS_FIXTURE;
        $fixture[self::PROGRAM_NAME]['name'] = null;

        $this->inject($provider, 'programTemplates', $fixture);

        self::expectException(EmptyNameException::class);
        self::expectExceptionCode(1635175957);

        $provider->getPrograms();
    }
}
