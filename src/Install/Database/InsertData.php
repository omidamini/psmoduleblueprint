<?php
/**
 * @author Omid AMINI
 * @link https://www.linkedin.com/in/omid-amini/
 * @license OSL 3.0
 */
declare(strict_types=1);

namespace Blueprint\Module\Psmoduleblueprint\Install\Database;

class InsertData implements SchemaInterface 
{
    private $tables = [];
    public function __construct(
        private ?\Db $db = null
    ) {
        if ($this->db === null) {
            $this->db = \Db::getInstance();
        }
        $this->initializeTables();
    }
    
    use SchemaTrait;

    private function initializeTables(): void
    {
        $context = \Context::getContext();
        $this->tables  = 
        [
            'psmoduleblueprint_config' => "INSERT INTO `" . _DB_PREFIX_ . "psmoduleblueprint_config` (`name`, `value`) VALUES ('current_language', '" . (int)$context->language->id . "'), ('current_shop', '" . (int)$context->shop->id . "');"
        ];
    }
    /**
     * @return bool
     * @throws \Exception
     */
    public function executeQuery(): bool
    {
        $queries = $this->getQueries();
        foreach ($queries as $query) {
            if (!$this->db->execute($query)) {
                throw new \Exception('Error executing query: ' . $query);
            }
        }
        return true;
    }
}