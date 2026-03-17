<?php
/**
 * @author Omid AMINI
 * @link https://www.linkedin.com/in/omid-amini/
 * @license OSL 3.0
 * @copyright 2026 ITIS COMMERCE (https://www.itis-commerce.com/)
 */
declare(strict_types=1);
namespace Blueprint\Module\Psmoduleblueprint\Install\Database;

use Db;

class QueryManager
{
    /**
     * @param \Db|null $db
     */
    public function __construct(private ?Db $db = null)
    {
        $this->db = $db ?? Db::getInstance();
    }
    /**
     * @return bool
     * @throws \Exception
     */
    public function executeQuery(string $schema): bool
    {
        if (!class_exists($schema)) {
            throw new \Exception('Class ' . $schema . ' does not exist');
        }

        $schemaInstance = new $schema();
        if (!$schemaInstance instanceof SchemaInterface) {
            throw new \Exception('Class ' . $schema . ' must implement SchemaInterface');
        }

        $queries = $schemaInstance->getQueries();
        foreach ($queries as $query) {
            if (!$this->db->execute($query)) {
                throw new \Exception('Error executing query: ' . $query);
            }
        }
        return true;
    }
    /**
     * @param string $schemaClass
     * @return QueryManager
     */
    public static function getInstance(?Db $db = null): QueryManager
    {
        return new self($db);
    }

}