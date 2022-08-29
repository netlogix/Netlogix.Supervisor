<?php
declare(strict_types=1);

namespace Netlogix\Supervisor\Model;

use Neos\Flow\Annotations as Flow;
use function array_values;
use function preg_replace;
use function strtolower;
use function trim;

final class Factory
{
    /**
     * @var array<string, Group>
     */
    protected $groups = [];

    /**
     * @var array<string, array{name: string, priority: int}>
     * @Flow\InjectConfiguration(package="Netlogix.Supervisor", path="groups")
     */
    protected $groupSettings = [];

    public function registerProgram(Program $program): void
    {
        $groupName = $program->getGroupName();
        if (!isset($this->groups[$groupName])) {
            $this->groups[$groupName] = $this->createGroupFromProgram($program);
        } else {
            $this->groups[$groupName] = $this->mergeGroupWithProgram($this->groups[$groupName], $program);
        }
    }

    /**
     * @return array<Group>
     */
    public function getGroups(): array
    {
        return array_values($this->groups);
    }

    protected function createGroupFromProgram(Program $program): Group
    {
        $groupName = $program->getGroupName();
        $groupSettings = $this->groupSettings[$groupName] ?? [];

        $identifier = $groupSettings['name'] ?? $groupName;
        $priority = $groupSettings['priority'] ?? 999;

        return new Group(
            $identifier,
            $priority,
            $program
        );
    }

    protected function mergeGroupWithProgram(Group $group, Program $program): Group
    {
        return $group->withProgram($program);
    }

    public static function removeInvalidCharactersFromSupervisorIdentifier(string $subject): string
    {
        /**
         * \p{xx}   A character with the xx unicode property.   https://php.net/manual/en/regexp.reference.escape.php
         * \pL      Every Letter                                https://php.net/manual/en/regexp.reference.unicode.php
         */
        $cleanupPattern = '%[^\\pL\\d]+%ium';

        $subject = (string)preg_replace($cleanupPattern, '-', $subject);
        $subject = trim($subject, '-');
        return strtolower($subject);
    }
}
