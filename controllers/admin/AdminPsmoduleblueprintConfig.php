<?php
/**
 * @author Omid AMINI
 * @link https://www.linkedin.com/in/omid-amini/
 * @license OSL 3.0
 */
use Blueprint\Module\Psmoduleblueprint\Factory\HookFactory;
use Blueprint\Module\Psmoduleblueprint\Hook\GetContentHook;

if (!defined('_PS_VERSION_')) {
    exit;
}

class AdminPsmoduleblueprintConfigController extends ModuleAdminController
{
    public function initContent()
    {
        parent::initContent();

        (new HookFactory($this->module, $this->context))
            ->create(GetContentHook::class)
            ->run();
    }
}
