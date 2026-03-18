<?php
/**
 * @author Omid AMINI
 * @link https://www.linkedin.com/in/omid-amini/
 * @license OSL 3.0
 */
namespace Blueprint\Module\Psmoduleblueprint\Install\Database;

trait  SchemaTrait {
   /**
     * @return array
     */
    public function getQueries(): array
    {
        $tables = $this->getTables();
        $sql[] = 'SET foreign_key_checks = 0;';
        foreach ($tables as $table => $query) {
            $sql[] = $query;
        }
        $sql[] = 'SET foreign_key_checks = 1;';
        return $sql;
    }
    /**
     * @return array
     */
    public function getTables(): array
    {
        return $this->tables;
    }
}