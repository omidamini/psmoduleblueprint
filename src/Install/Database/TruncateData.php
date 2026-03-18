<?php
/**
 * @author Omid AMINI
 * @link https://www.linkedin.com/in/omid-amini/
 * @license OSL 3.0
 */
declare(strict_types=1);
namespace Blueprint\Module\Psmoduleblueprint\Install\Database;

class TruncateData implements SchemaInterface 
{
    
    public function __construct(private ?\Db $db = null) {
        if ($this->db === null) 
        {
            $this->db = \Db::getInstance();
        }
    }
    
    use SchemaTrait;

    /**
     * @var array
     */
    private array $tables  = [
        'psmoduleblueprint_config' => "TRUNCATE TABLE `" . _DB_PREFIX_ . "psmoduleblueprint_config`;"
    ];
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