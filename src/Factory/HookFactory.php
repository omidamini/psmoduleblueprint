<?php
/**
 * @author Omid AMINI
 * @link https://www.linkedin.com/in/omid-amini/
 * @license OSL 3.0
 */
declare(strict_types=1);

namespace Blueprint\Module\Psmoduleblueprint\Factory;

use Blueprint\Module\Psmoduleblueprint\Hook\Hook;
use Context;
use Module;

/**
 * Factory Method: builds a hook object only when the class extends Hook.
 */
class HookFactory
{
    public function __construct(private Module $module, private Context $context)
    {
    }

    public function create(string $hookClassName): Hook
    {
        if (!is_subclass_of($hookClassName, Hook::class)) {
            throw new \InvalidArgumentException('Hook class must extend ' . Hook::class . '.');
        }

        return new $hookClassName($this->module, $this->context);
    }
}
