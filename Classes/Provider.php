<?php
declare(strict_types=1);

namespace Netlogix\Supervisor;

use Netlogix\Supervisor\Model;

interface Provider
{
//    /**
//     * @return Model\Group
//     */
//    public function getGroup(): Model\Group;

    /**
     * @return array<Model\Program>
     */
    public function getPrograms(): array;
}
