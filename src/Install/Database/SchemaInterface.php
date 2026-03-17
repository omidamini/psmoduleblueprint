<?php
/**
 * @author Omid AMINI
 * @link https://www.linkedin.com/in/omid-amini/
 * @license OSL 3.0
 * @copyright 2026 ITIS COMMERCE (https://www.itis-commerce.com/)
 */
namespace Blueprint\Module\Psmoduleblueprint\Install\Database;
interface SchemaInterface
{
    /**
     * @return array
     */
    public function getQueries(): array;

    /**
     * @return array
     */
    public function getTables(): array;
}