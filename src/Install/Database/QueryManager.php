<?php
/**
 * @author Omid AMINI
 * @link https://www.linkedin.com/in/omid-amini/
 * @license OSL 3.0
 */
declare(strict_types=1);

namespace Blueprint\Module\Psmoduleblueprint\Install\Database;

use Db;

class QueryManager
{
    public function __construct(private ?Db $db = null)
    {
        $this->db = $db ?? Db::getInstance();
    }

    public function execute(SchemaInterface $schema): bool
    {
        $this->db->execute('SET FOREIGN_KEY_CHECKS = 0');

        try {
            foreach ($schema->getQueries() as $query) {
                $this->assertSchemaStatement($query);
                if (!$this->db->execute($query)) {
                    throw new \RuntimeException('Schema query failed: ' . $this->db->getMsgError());
                }
            }
        } finally {
            $this->db->execute('SET FOREIGN_KEY_CHECKS = 1');
        }

        return true;
    }

    private function assertSchemaStatement(string $query): void
    {
        $statement = trim($query);
        $withoutTerminator = rtrim($statement, " \t\n\r;");

        if ($statement === '' || str_contains($withoutTerminator, ';') || str_contains($statement, "\0")) {
            throw new \InvalidArgumentException('Multiple SQL statements are not allowed.');
        }

        if (!preg_match('/^(CREATE|ALTER|DROP|TRUNCATE|RENAME)\b/i', $withoutTerminator)) {
            throw new \InvalidArgumentException('Only schema statements can be executed.');
        }
    }
}
