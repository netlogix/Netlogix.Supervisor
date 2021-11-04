<?php
declare(strict_types=1);

namespace Netlogix\Supervisor\Model;

use Netlogix\Supervisor\Exception\Group\EmptyGroupException;
use function array_merge;
use function count;
use function sprintf;

final class Group
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var int
     */
    private $priority;

    /**
     * @var Program[]
     */
    protected $programs;

    public function __construct(string $name, int $priority = 999, Program ...$programs)
    {
        $this->name = Factory::removeInvalidCharactersFromSupervisorIdentifier($name);
        $this->priority = $priority;
        $this->programs = $programs;
        if (count($programs) === 0) {
            throw new EmptyGroupException(
                sprintf('Supervisor groups must not be empty, "%s" is.', $name),
                1634910620
            );
        }
    }

    public function getName(): string
    {
        return $this->name;
    }


    public function getPriority(): int
    {
        return $this->priority;
    }

    /**
     * @return array<string, string|int|bool>
     */
    public function getArguments(): array
    {
        return [
            'priority' => $this->priority
        ];
    }

    /**
     * @return Program[]
     */
    public function getPrograms(): array
    {
        return $this->programs;
    }

    public function withProgram(Program $program): self
    {
        return new static(
            $this->name,
            $this->priority,
            ... array_merge($this->programs, [$program])
        );
    }
}
