<?php
/**
 * @author Omid AMINI
 * @link https://www.linkedin.com/in/omid-amini/
 * @license OSL 3.0
 */
declare(strict_types=1);

namespace Blueprint\Module\Psmoduleblueprint\Install\Database;

class CreateSchema implements SchemaInterface
{
    use SchemaTrait;

    private array $tables = [
        'psmoduleblueprint_conf' => 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'psmoduleblueprint_conf` (
            `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `name` VARCHAR(64) NOT NULL,
            `value` VARCHAR(255) NOT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `uniq_psmoduleblueprint_conf_name` (`name`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci',
    ];
}
