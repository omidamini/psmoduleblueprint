<?php
/**
 * @author Omid AMINI
 * @link https://www.linkedin.com/in/omid-amini/
 * @license OSL 3.0
 */
declare(strict_types=1);

namespace Blueprint\Module\Psmoduleblueprint\Install\Hook;

use Module;

class HookManager
{
    private array $hooks;

    public function __construct(private Module $module, HooksList $hooksList)
    {
        $this->hooks = $hooksList->getHooks();
    }

    public function registerHooks(): bool
    {
        foreach ($this->hooks as $hook) {
            if (!is_string($hook) || !preg_match('/^[A-Za-z0-9]+$/', $hook)) {
                throw new \InvalidArgumentException('Invalid hook name.');
            }
            if (!$this->module->registerHook($hook)) {
                throw new \RuntimeException('Failed to register hook ' . $hook . '.');
            }
        }

        return true;
    }

    public function unregisterHooks(): bool
    {
        foreach ($this->hooks as $hook) {
            if (is_string($hook)) {
                $this->module->unregisterHook($hook);
            }
        }

        return true;
    }
}
