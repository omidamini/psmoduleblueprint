<?php
/**
 * @author Omid AMINI
 * @link https://www.linkedin.com/in/omid-amini/
 * @license OSL 3.0
 * @copyright 2026 ITIS COMMERCE (https://www.itis-commerce.com/)
 */
declare(strict_types=1);
namespace Blueprint\Module\Psmoduleblueprint\Install;
use Context;
use Blueprint\Module\Psmoduleblueprint\Install\Database\CreateSchema;
use Blueprint\Module\Psmoduleblueprint\Install\Database\DropSchema;
use Module;
use Blueprint\Module\Psmoduleblueprint\Install\Database\QueryManager;
use Blueprint\Module\Psmoduleblueprint\Install\Hook\HookManager;
use Blueprint\Module\Psmoduleblueprint\Install\Tabmenu\TabMenuManger;

final class Installer
{
    public function __construct(
        private Module $module,
        private Context $context,
        private HookManager $hookManager,
        private QueryManager $queryManager,
        private TabMenuManger $tabMenuManger,

    ) {}

    public function installHooks(): bool
    {
        try 
        {
            $this->module->confirmation('Installing hooks');
            return $this->hookManager->registerHooks();
        } catch (\Throwable $th) {
            $this->module->error('Error installing hooks.  File: ' . $th->getFile() . ' Line: ' . $th->getLine() . ' Message: ' . $th->getMessage());
            return false;
        }
    }

    public function uninstallHooks(): bool
    {
        try
        {
            $this->module->confirmation('Uninstalling hooks');
            return $this->hookManager->unregisterHooks();
        } catch (\Throwable $th) {
            $this->module->error('Error uninstalling hooks.  File: ' . $th->getFile() . ' Line: ' . $th->getLine() . ' Message: ' . $th->getMessage());
            return false;
        }
    }
    public function installQueries(): bool
    {
        try {
            $this->module->confirmation('Installing queries');
            return $this->queryManager->executeQuery(CreateSchema::class);
        } catch (\Throwable $th) {
            $this->module->error('Error installing queries. File: ' . $th->getFile() . ' Line: ' . $th->getLine() . ' Message: ' . $th->getMessage());
            return false;
        }
    }

    public function uninstallQueries(): bool
    {
        try 
        {
            $this->module->confirmation('Uninstalling queries');
            return $this->queryManager->executeQuery(DropSchema::class);
        } catch (\Throwable $th) {
            $this->module->error('Error uninstalling queries.  File: ' . $th->getFile() . ' Line: ' . $th->getLine() . ' Message: ' . $th->getMessage());
            return false;
        }
    }
    public function installTabMenus(): bool
    {
        
        try {
            $this->module->confirmation('Installing tab menu');
            return $this->tabMenuManger->registerMenus();
        } catch (\Throwable $th) {
            $this->module->error('Error installing tab menu. File: ' . $th->getFile() . ' Line: ' . $th->getLine() . ' Message: ' . $th->getMessage());
            return false;
        }
    }
    public function uninstallTabMenu(): bool
    {
        try {
            $this->module->confirmation('Uninstalling tab menu');
            return $this->tabMenuManger->unregisterMenus();
        } catch (\Throwable $th) {
            $this->module->error('Error uninstalling tab menu. File: ' . $th->getFile() . ' Line: ' . $th->getLine() . ' Message: ' . $th->getMessage());
            return false;
        }
    }
}