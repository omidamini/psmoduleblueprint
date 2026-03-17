<?php
/**
 * @author Omid AMINI
 * @link https://www.linkedin.com/in/omid-amini/
 * @license OSL 3.0
 * @copyright 2026 ITIS COMMERCE (https://www.itis-commerce.com/)
 */
declare(strict_types=1);
namespace Blueprint\Module\Psmoduleblueprint\Install\Hook;
use Module;

class HookManager
{
    /**
     * @var array
     */
    private array $hooks;

    /**
     * @param Module $module
     * @param HooksList $hooksList
     */
    public function __construct(private Module $module, private HooksList $hooksList)
    {
        $this->hooks = $hooksList->getHooks();
    }
    public function registerHooks(): bool
    {
        $hooks = $this->hooks;
        foreach ($hooks as $hook) {
            if (!$this->module->registerHook($hook)) {
                throw new \Exception('Failed to register hook : ' . $hook);
            }
        }
        return true;
    }
    public function unregisterHooks(): bool
    {
        $hooks = $this->hooks;
        foreach ($hooks as $hook) {
            if (!$this->module->unregisterHook($hook)) {
                throw new \Exception('Failed to unregister hook : ' . $hook);
            }
        }
        return true;
    }
    public static function getInstance(Module $module, string $hooksList): HookManager
    {
        return new self($module, new $hooksList());
    }
}