<?php
/**
 * @author Omid AMINI
 * @link https://www.linkedin.com/in/omid-amini/
 * @license OSL 3.0
 * @copyright 2026 ITIS COMMERCE (https://www.itis-commerce.com/)
 */
declare(strict_types=1);
namespace Blueprint\Module\Psmoduleblueprint\Factory;
use Blueprint\Module\Psmoduleblueprint\Install\Database\QueryManager;
use Blueprint\Module\Psmoduleblueprint\Install\Hook\HookManager;
use Blueprint\Module\Psmoduleblueprint\Install\Hook\HooksList;
use Blueprint\Module\Psmoduleblueprint\Install\Installer;
use Blueprint\Module\Psmoduleblueprint\Install\Tabmenu\MenusList;
use Blueprint\Module\Psmoduleblueprint\Install\Tabmenu\TabMenuManger;
use Module;
use Context;

class InstallFactory
{
    public function __construct(private Module $module, private Context $context)
    {
        
    }
    public function create(string $installer): Installer
    {
        if (!class_exists($installer)) {
            throw new \InvalidArgumentException('Installer class does not exist: ' . $installer);
        }
        
        return new $installer(
            $this->module,
            $this->context,
            HookManager::getInstance($this->module, HooksList::class),
            QueryManager::getInstance(),
            TabMenuManger::getInstance($this->module->name, MenusList::class, $this->module->get('doctrine.orm.entity_manager') ?: null)
        );
    }
}