<?php
/**
 * @author Omid AMINI
 * @link https://www.linkedin.com/in/omid-amini/
 * @license OSL 3.0
 * @copyright 2026 ITIS COMMERCE (https://www.itis-commerce.com/)
 */
declare(strict_types=1);
namespace Blueprint\Module\Psmoduleblueprint\Install\Database;
class DropSchema implements SchemaInterface{
    
    use SchemaTrait;
    /**
     * @var array
     */
    private array $tables = [];
    public function __construct(string $schema = CreateSchema::class)
    {
        if (!class_exists($schema)) {
            throw new \Exception('Class ' . $schema . ' does not exist');
        }
        $schemaInstance = new $schema();
        if (!$schemaInstance instanceof SchemaInterface) {
            throw new \Exception('Class ' . $schema . ' must implement SchemaInterface');
        }
        $schemaTables = $schemaInstance->getTables();
        foreach ($schemaTables as $key => $query) {
            $this->tables[$key] = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . $key . '`;';
        }
    }
}