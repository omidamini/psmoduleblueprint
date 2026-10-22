<?php
/**
 * @author Omid AMINI
 * @link https://www.linkedin.com/in/omid-amini/
 * @license OSL 3.0
 */
declare(strict_types=1);

namespace Blueprint\Module\Psmoduleblueprint\Install\Database;

trait SchemaTrait
{
    public function getQueries(): array
    {
        return array_values($this->getTables());
    }

    public function getTables(): array
    {
        return $this->tables;
    }
}
