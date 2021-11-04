<?php
declare(strict_types=1);

namespace Netlogix\Supervisor\Model;

use Neos\Flow\Core\Bootstrap;
use function array_filter;
use function is_null;
use function sprintf;

final class Program
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $groupName;

    /**
     * @var array<string, int|bool|string|null>
     */
    private $arguments = [];

    /**
     * @param string $name
     * @param string $groupName
     * @param string $command
     * @param array<string, int|bool|string|null> $settings
     */
    public function __construct(
        string $name,
        string $groupName,
        string $command,
        array $settings = []
    ) {
        assert(\defined('FLOW_PATH_ROOT'));
        $this->name = Factory::removeInvalidCharactersFromSupervisorIdentifier($name);
        $this->groupName = $groupName;
        // FIXME: Validate Settings to ensure exceptions occur in PHP
        $this->arguments = $settings;
        $this->arguments['command'] = $command;
        $this->arguments['directory'] = $this->arguments['directory'] ?? \FLOW_PATH_ROOT;
        $this->arguments['environment'] = $this->arguments['environment'] ??
            sprintf(
                "FLOW_CONTEXT='%s'",
                Bootstrap::getEnvironmentConfigurationSetting('FLOW_CONTEXT') ?: 'Development'
            );
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getGroupName(): string
    {
        return $this->groupName;
    }

    /**
     * @return array<string, string|int|bool>
     */
    public function getArguments(): array
    {
        return array_filter(
            $this->arguments,
            static function ($value) {
                return !is_null($value);
            }
        );
    }
}
