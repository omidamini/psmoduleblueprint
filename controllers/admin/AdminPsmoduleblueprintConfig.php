<?php
/**
 * @author Omid AMINI
 * @link https://www.linkedin.com/in/omid-amini/
 * @license OSL 3.0
 */
use Blueprint\Module\Psmoduleblueprint\Factory\HookFactory;
use Blueprint\Module\Psmoduleblueprint\Hook\GetContentHook;
class AdminPsmoduleblueprintConfigController extends ModuleAdminController
{
    public function initContent()
    {
        parent::initContent();
        return (new HookFactory($this->module, Context::getContext()))
            ->create(GetContentHook::class)
            ->run();
    }
}