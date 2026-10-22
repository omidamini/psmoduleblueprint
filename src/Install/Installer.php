<?php
/**
 * @author Omid AMINI
 * @link https://www.linkedin.com/in/omid-amini/
 * @license OSL 3.0
 */
declare(strict_types=1);

namespace Blueprint\Module\Psmoduleblueprint\Install;

use Blueprint\Module\Psmoduleblueprint\Install\Database\CreateSchema;
use Blueprint\Module\Psmoduleblueprint\Install\Database\DropSchema;
use Blueprint\Module\Psmoduleblueprint\Install\Database\QueryManager;
use Blueprint\Module\Psmoduleblueprint\Install\Hook\HookManager;
use Blueprint\Module\Psmoduleblueprint\Install\Tabmenu\TabMenuManager;
use Module;

final class Installer
{
    public function __construct(
        private Module $module,
        private HookManager $hookManager,
        private QueryManager $queryManager,
        private TabMenuManager $tabMenuManager,
    ) {
    }

    public function installHooks(): bool
    {
        try {
            $this->hookManager->registerHooks();
            $this->module->confirmation('Hooks installed.');

            return true;
        } catch (\Throwable $throwable) {
            $this->module->logThrowable('Hook installation failed.', $throwable);

            return false;
        }
    }

    public function uninstallHooks(): bool
    {
        try {
            $this->hookManager->unregisterHooks();

            return true;
        } catch (\Throwable $throwable) {
            $this->module->logThrowable('Hook removal failed.', $throwable);

            return false;
        }
    }

    public function installQueries(): bool
    {
        try {
            $this->queryManager->execute(new CreateSchema());
            $this->module->confirmation('Database installed.');

            return true;
        } catch (\Throwable $throwable) {
            $this->module->logThrowable('Database installation failed.', $throwable);

            return false;
        }
    }

    public function uninstallQueries(): bool
    {
        try {
            $this->queryManager->execute(new DropSchema());

            return true;
        } catch (\Throwable $throwable) {
            $this->module->logThrowable('Database removal failed.', $throwable);

            return false;
        }
    }

    public function installTabMenus(): bool
    {
        try {
            $this->tabMenuManager->registerMenus();
            $this->module->confirmation('Admin menu installed.');

            return true;
        } catch (\Throwable $throwable) {
            $this->module->logThrowable('Admin menu installation failed.', $throwable);

            return false;
        }
    }

    public function uninstallTabMenu(): bool
    {
        try {
            $this->tabMenuManager->unregisterMenus();

            return true;
        } catch (\Throwable $throwable) {
            $this->module->logThrowable('Admin menu removal failed.', $throwable);

            return false;
        }
    }
}
