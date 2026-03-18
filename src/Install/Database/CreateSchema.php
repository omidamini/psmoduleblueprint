<?php
/**
 * @author Omid AMINI
 * @link https://www.linkedin.com/in/omid-amini/
 * @license OSL 3.0
 */
declare(strict_types=1);
namespace Blueprint\Module\Psmoduleblueprint\Install\Database;
class CreateSchema implements SchemaInterface{    
    use SchemaTrait;
    /**
     * @var array
     */
    private array $tables = [
        'psmoduleblueprint_conf' => 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'psmoduleblueprint_conf` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `name` VARCHAR(255) NOT NULL,
            `value` VARCHAR(255) NOT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8MB4 COLLATE=UTF8MB4_UNICODE_CI;'
    ];
}