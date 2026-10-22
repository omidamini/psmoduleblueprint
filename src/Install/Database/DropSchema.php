<?php
/**
 * @author Omid AMINI
 * @link https://www.linkedin.com/in/omid-amini/
 * @license OSL 3.0
 */
declare(strict_types=1);

namespace Blueprint\Module\Psmoduleblueprint\Install\Database;

class DropSchema implements SchemaInterface
{
    use SchemaTrait;

    private array $tables = [];

    public function __construct()
    {
        foreach (array_keys((new CreateSchema())->getTables()) as $key) {
            if (!preg_match('/^[A-Za-z0-9_]+$/', (string) $key) || !preg_match('/^[A-Za-z0-9_]+$/', _DB_PREFIX_)) {
                throw new \InvalidArgumentException('Unsafe table name.');
            }

            $this->tables[$key] = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . $key . '`';
        }
    }
}
