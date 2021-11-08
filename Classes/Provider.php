<?php
declare(strict_types=1);

namespace Netlogix\Supervisor;

use Netlogix\Supervisor\Model;

interface Provider
{
    /**
     * @return array<Model\Program>
     */
    public function getPrograms(): array;
}
