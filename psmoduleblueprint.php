<?php
/**
 * @author Omid AMINI
 * @link https://www.linkedin.com/in/omid-amini/
 * @license OSL 3.0
 */

use Blueprint\Module\Psmoduleblueprint\Factory\HookFactory;
use Blueprint\Module\Psmoduleblueprint\Factory\InstallFactory;
use Blueprint\Module\Psmoduleblueprint\Hook\ActionFrontControllerSetMediaHook;
use Blueprint\Module\Psmoduleblueprint\Hook\GetContentHook;
use Blueprint\Module\Psmoduleblueprint\Install\Installer;
use Blueprint\Module\Psmoduleblueprint\Service\LoggerService;

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once __DIR__ . '/vendor/autoload.php';

class Psmoduleblueprint extends Module
{
    private ?Installer $installer = null;
    private ?LoggerService $loggerService = null;

    public function __construct()
    {
        $this->name = 'psmoduleblueprint';
        $this->tab = 'others';
        $this->version = '1.1.0';
        $this->author = 'Omid AMINI';
        $this->need_instance = 0;
        $this->bootstrap = true;
        $this->ps_versions_compliancy = ['min' => '8.0.0', 'max' => '8.99.99'];
        parent::__construct();
        $this->displayName = $this->l('ps module blueprint');
        $this->description = $this->l('Integrates ps module blueprint into your PrestaShop store.');
    }

    public function getLoggerService(): ?LoggerService
    {
        if ($this->loggerService instanceof LoggerService) {
            return $this->loggerService;
        }

        try {
            $service = $this->get(LoggerService::class);
        } catch (\Throwable) {
            return null;
        }

        if ($service instanceof LoggerService) {
            $this->loggerService = $service;
        }

        return $this->loggerService;
    }

    public function install()
    {
        if (!parent::install()) {
            return false;
        }

        try {
            $installer = $this->getInstaller();
        } catch (\Throwable $throwable) {
            $this->logThrowable('Module installation failed.', $throwable);
            parent::uninstall();

            return false;
        }

        $installed = $installer->installHooks()
            && $installer->installQueries()
            && $installer->installTabMenus();

        if (!$installed) {
            $this->uninstall();

            return false;
        }

        return true;
    }

    public function uninstall()
    {
        try {
            $installer = $this->getInstaller();
            $installer->uninstallTabMenu();
            $installer->uninstallQueries();
            $installer->uninstallHooks();
        } catch (\Throwable $throwable) {
            $this->logThrowable('Module removal failed.', $throwable);
        }

        return parent::uninstall();
    }

    public function hookActionFrontControllerSetMedia(array $params)
    {
        $this->hookFactory()
            ->create(ActionFrontControllerSetMediaHook::class)
            ->setParams($params)
            ->run();
    }

    public function getContent()
    {
        $this->hookFactory()->create(GetContentHook::class)->run();
    }

    public function isUsingNewTranslationSystem()
    {
        return true;
    }

    public function error(string $message): void
    {
        $this->_errors[] = $message;
        $this->writeLog('error', $message);
    }

    public function confirmation(string $message): void
    {
        $this->_confirmations[] = $message;
        $this->writeLog('info', $message);
    }

    public function logThrowable(string $message, \Throwable $throwable): void
    {
        $this->_errors[] = $message;
        $this->writeLog(
            'error',
            $message . ' ' . $throwable->getMessage()
            . ' at ' . $throwable->getFile() . ':' . $throwable->getLine()
        );
    }

    private function getInstaller(): Installer
    {
        if (!$this->installer instanceof Installer) {
            $this->installer = (new InstallFactory($this))->create();
        }

        return $this->installer;
    }

    private function hookFactory(): HookFactory
    {
        return new HookFactory($this, $this->context);
    }

    private function writeLog(string $level, string $message): void
    {
        try {
            $logger = $this->getLoggerService();
            if (!$logger instanceof LoggerService) {
                throw new \RuntimeException('Logger is not available.');
            }

            if ($level === 'error') {
                $logger->error($message);

                return;
            }

            $logger->info($message);
        } catch (\Throwable) {
            if (class_exists(PrestaShopLogger::class)) {
                PrestaShopLogger::addLog(
                    '[' . $this->name . '] ' . $message,
                    $level === 'error' ? 3 : 1,
                    null,
                    null,
                    null,
                    true
                );
            }
        }
    }
}
