<?php
declare(strict_types=1);

namespace Netlogix\Supervisor\ConfigurationBased;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\ObjectManagement\ObjectManagerInterface;
use Netlogix\Supervisor\Exception\Program\EmptyCommandException;
use Netlogix\Supervisor\Exception\Program\EmptyNameException;
use Netlogix\Supervisor\Model;
use Netlogix\Supervisor\Provider as ProviderInterface;
use function is_array;

class Provider implements ProviderInterface
{
    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var array<string, array<string|int|bool>>
     * @Flow\InjectConfiguration(package="Netlogix.Supervisor", path="programs")
     */
    protected $programTemplates;

    /**
     * @var ?array<Model\Program>
     * @phpstan-var ?list<Model\Program>
     */
    protected $programs;

    public function initialize(): void
    {
        if (is_array($this->programs)) {
            return;
        }
        $this->programs = [];
        foreach ($this->programTemplates as $programIndex => $programTemplate) {

            $name = trim((string)($programTemplate['name'] ?? ''));
            $command = trim((string)($programTemplate['command'] ?? ''));
            $groupName = trim((string)($programTemplate['groupName'] ?? 'default'));

            if ($name === '') {
                throw new EmptyNameException(
                    sprintf('The program "%s" has no name', $programIndex),
                    1635175957
                );
            }
            if ($command === '') {
                throw new EmptyCommandException(
                    sprintf('The program "%s" has no command', $programTemplate['name']),
                    1635176018
                );
            }

            unset($programTemplate['name']);
            unset($programTemplate['command']);
            unset($programTemplate['groupName']);

            $groups[$groupName] = $groups[$groupName] ?? [];
            $groups[$groupName][$name] = $name;

            $this->programs[] = new Model\Program(
                $name,
                $groupName,
                $command,
                $programTemplate
            );
        }
    }

    /**
     * @return array<Model\Program>
     * @phpstan-return list<Model\Program>
     */
    public function getPrograms(): array
    {
        $this->initialize();
        return $this->programs ?? [];
    }
}
