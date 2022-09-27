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

    public function startGroupsCommand(): void
    {
        $groups = $this->getGroups();
        array_map([$this, 'supervisorctlGroupAction'], $groups, array_fill(0, count($groups), 'start'));
    }

    public function stopGroupsCommand(): void
    {
        $groups = $this->getGroups();
        array_map([$this, 'supervisorctlGroupAction'], $groups, array_fill(0, count($groups), 'stop'));
    }

    public function updateGroupsCommand(): void
    {
        $groups = $this->getGroups();


        array_map([$this, 'supervisorctlGroupAction'], $groups, array_fill(0, count($groups), 'update'));
    }

    /**
     * @param string $action
     * @param Model\Group $group
     * @return void
     */
    protected function supervisorctlGroupAction(Model\Group $group, string $action)
    {
        $output = shell_exec(sprintf('sudo supervisorctl %s %s%s',$action, $group->getName(), $action != 'update' ? ':' : '') . PHP_EOL);
        if (is_string($output)) {
            $this->output($output);
        }
    }

    /**
     * @return Model\Factory
     */
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

    /**
     * @param string $type
     * @param string $name
     * @return string
     */
    private static function fileName(string $type, string $name): string
    {
        $filePattern = '%s-%s.conf';
        return self::CONFIG_PATH . sprintf($filePattern, $type, $name);
    }
}
