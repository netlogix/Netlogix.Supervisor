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

    /**
     * Creates supervisor configuration
     */
    public function createCommand(): void
    {
        Files::createDirectoryRecursively(self::CONFIG_PATH);
        Files::emptyDirectoryRecursively(self::CONFIG_PATH);

        $renderer = new Renderer\Renderer();

        foreach ($this->getGroups() as $group) {

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
     * Starts all programs of configured groups
     */
    public function startGroupsCommand(): void
    {
        $this->runSupervisorCommand('start', ...$this->getGroups());
    }

    /**
     * Stops all programs of configured groups
     */
    public function stopGroupsCommand(): void
    {
        $this->runSupervisorCommand('stop', ...$this->getGroups());
    }

    /**
     * Reloads config and starts/stops programs accordingly
     */
    public function updateGroupsCommand(): void
    {
        $this->runSupervisorCommand('update', ...$this->getGroups());
    }

    protected function runSupervisorCommand(string $action, Model\Group ...$groups): bool
    {
        $output = [];
        $groupArgs = implode(' ', array_map(function($i){return $i->getName();}, $groups));
        $command = sprintf('supervisorctl %s %s%s 2>&1',escapeshellarg($action), escapeshellarg($groupArgs), $action !== 'update' ? ':' : '');

        exec($command, $output, $result);
        $output = implode("\n", $output);
        if ($result !== 0) {
            if (!empty($output)) {
                $exceptionMessage = $output;
            } else {
                $exceptionMessage = sprintf('Execution of supervisorctl failed with exit code %d without any further output.', $result);
            }
            $this->outputLine($exceptionMessage);
        }

        return $result === 0;
    }

    protected function bootstrapFactory(): Model\Factory
    {
        $factory = new Model\Factory();

        foreach ($this->getProviders() as $provider) {
            assert($provider instanceof Provider);
            foreach ($provider->getPrograms() as $program) {
                assert($program instanceof Model\Program);
                $factory->registerProgram($program);
            }
        }

        return $factory;
    }

    /**
     * @return array<Model\Group>
     */
    protected function getGroups(): array
    {
        $factory = $this->bootstrapFactory();
        return $factory->getGroups();
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
