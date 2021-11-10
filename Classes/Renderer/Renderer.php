<?php
declare(strict_types=1);

namespace Netlogix\Supervisor\Renderer;

use Netlogix\Supervisor\Model;
use function array_map;
use function is_bool;
use function is_null;
use function join;
use function sprintf;
use function str_replace;
use const PHP_EOL;

final class Renderer
{
    public function renderGroup(Model\Group $group): string
    {
        $content = '[group:' . $group->getName() . ']' . PHP_EOL;

        foreach ($group->getArguments() as $key => $value) {
            $content .= $key . '=' . $value . PHP_EOL;
        }

        $programNames = array_map(
            static function (Model\Program $program) {
                return $program->getName();
            },
            $group->getPrograms()
        );
        $content .= 'programs=' . join(', ', $programNames) . PHP_EOL;

        return $content;
    }

    public function renderProgram(Model\Program $program): string
    {
        $content = '[program:' . $program->getName() . ']' . PHP_EOL;

        foreach ($program->getArguments() as $key => $value) {
            if (is_bool($value)) {
                $value = $value ? 'true' : 'false';
            } elseif (is_string($value)) {
                $value = self::escape_ini_value($value);
            }
            $content .= $key . '=' . $value . PHP_EOL;
        }

        return $content;
    }

    private function escape_ini_value(string $value): string
    {
        static $replacement;
        if (is_null($replacement)) {
            $replacement = [];
            // TODO: Which characters have to be replaced?
            foreach ([PHP_EOL, '\\'] as $specialChar) {
                $replacement[$specialChar] = '\\' . $specialChar;
            }
        }
        $value = str_replace(array_keys($replacement), array_values($replacement), $value);
        return sprintf('%s', $value);
    }
}
