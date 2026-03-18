<?php
/**
 * @author Omid AMINI
 * @link https://www.linkedin.com/in/omid-amini/
 * @license OSL 3.0
 */

use Blueprint\Module\Psmoduleblueprint\Factory\HookFactory;
use Blueprint\Module\Psmoduleblueprint\Factory\InstallFactory;
use Blueprint\Module\Psmoduleblueprint\Hook\ActionFrontControllerSetMediaHook;
use Blueprint\Module\Psmoduleblueprint\Hook\DisplayCustomerAccountHook;
use Blueprint\Module\Psmoduleblueprint\Hook\GetContentHook;
use Blueprint\Module\Psmoduleblueprint\Install\Installer;
use Blueprint\Module\Psmoduleblueprint\Service\LoggerService;

if(!defined('_PS_VERSION_')){
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
        $this->version = '1.0.0';
        $this->author = 'Omid AMINI';
        $this->need_instance = 0;
        $this->bootstrap = true;
        $this->ps_versions_compliancy = array('min' => '9.0', 'max' => _PS_VERSION_);
        parent::__construct();
        $this->displayName = $this->l('ps module blueprint');
        $this->description = $this->l('Integrates ps module blueprint into your PrestaShop store.');
    }
    public function getLoggerService(): ?LoggerService
    {
        return $this->loggerService ?? $this->get('Blueprint\Module\Psmoduleblueprint\Service\LoggerService');
    }
    private function getInstaller(): Installer
    {
        if ($this->installer === null) {
            $this->installer = (new InstallFactory($this, Context::getContext()))->create(Installer::class);
        }
        return $this->installer;
    }
    public function install()
    {
        if (!parent::install()) {
            return false;
        }
        $installer = $this->getInstaller();

        if(!$installer->installHooks()) {
            return false;
        }
        if(!$installer->installQueries()) {
            return false;
        }
        if(!$installer->installTabMenus()) {
            return false;
        }
        return true;
    }
    public function uninstall()
    {
        if (!parent::uninstall()) {
            return false;
        }
        $installer = $this->getInstaller();

        if(!$installer->uninstallHooks()) {
            return false;
        }
        if(!$installer->uninstallQueries()) {
            return false;
        }
        // Unregister the tab
        if(!$installer->uninstallTabMenu()) {
            return false;
        }
        return true;
    }
    public function hookActionFrontControllerSetMedia($params)
    {
        return (new HookFactory($this, Context::getContext()))
                ->create(ActionFrontControllerSetMediaHook::class)
                ->run();
    }
    // public function hookDisplayCustomerAccount()
    // {
    //     return (new HookFactory($this, Context::getContext()))
    //             ->create(DisplayCustomerAccountHook::class)
    //             ->run();
    // }
    public function getContent()
    {
        return (new HookFactory($this, Context::getContext()))
            ->create(GetContentHook::class)
            ->run();
    }
    public function isUsingNewTranslationSystem()
    {
        return true;
    }
    /**
    * Log an error message
    *
    * @param string $message The error message to log
    */
    public function error($message): void
    {
        $this->_errors[] = $message;
        try {
            $logger = $this->getLoggerService();
            if ($logger) {
                $logger->error($message);
            }
        } catch (\Throwable $th) {
            file_put_contents(_PS_MODULE_DIR_.DIRECTORY_SEPARATOR.$this->name. '/psmoduleblueprint.log', $message.PHP_EOL, FILE_APPEND);
        }
    }
    /**
    * confirmation message
    */
    public function confirmation($message): void
    {
        $this->_confirmations[] = $message;
        try {
            $logger = $this->getLoggerService();
            if ($logger) {
                $logger->info($message);
            }
        } catch (\Throwable $th) {
            file_put_contents(_PS_MODULE_DIR_.DIRECTORY_SEPARATOR.$this->name. '/psmoduleblueprint.log', $message.PHP_EOL, FILE_APPEND);
        }
    }
}