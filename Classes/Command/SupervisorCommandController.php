<?php
declare(strict_types=1);

namespace Netlogix\Supervisor\Command;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Cli\CommandController;
use Neos\Utility\Files;
use Netlogix\Supervisor\Model;
use Netlogix\Supervisor\Provider;
use Netlogix\Supervisor\Renderer;
use function array_map;
use function assert;
use function file_put_contents;
use function sprintf;

class SupervisorCommandController extends CommandController
{
    private const CONFIG_PATH = FLOW_PATH_CONFIGURATION . 'Supervisor/';

    /**
     * @var array<class-string<Provider>>
     * @Flow\InjectConfiguration(package="Netlogix.Supervisor", path="providers")
     */
    protected $providerClassNames = [];

    public function createCommand(): void
    {
        Files::createDirectoryRecursively(self::CONFIG_PATH);
        Files::emptyDirectoryRecursively(self::CONFIG_PATH);

        $factory = new Model\Factory();

        foreach ($this->getProviders() as $provider) {
            assert($provider instanceof Provider);
            foreach ($provider->getPrograms() as $program) {
                assert($program instanceof Model\Program);
                $factory->registerProgram($program);
            }
        }

        $renderer = new Renderer\Renderer();

        foreach ($factory->getGroups() as $group) {

            assert($group instanceof Model\Group);
            file_put_contents(
                self::fileName('group', $group->getName()),
                $renderer->renderGroup($group)
            );

            foreach ($group->getPrograms() as $program) {
                file_put_contents(
                    self::fileName('program', $program->getName()),
                    $renderer->renderProgram($program)
                );
            }
        }

    }

    /**
     * @return array<Provider>
     */
    protected function getProviders(): array
    {
        $factory = function(string $providerClassName): Provider {
            $provider = $this->objectManager->get($providerClassName);
            assert($provider instanceof Provider);
            return $provider;
        };
        return array_map(
            $factory,
            $this->providerClassNames
        );
    }

    private static function fileName(string $type, string $name): string
    {
        $filePattern = '%s-%s.conf';
        return self::CONFIG_PATH . sprintf($filePattern, $type, $name);
    }
}
