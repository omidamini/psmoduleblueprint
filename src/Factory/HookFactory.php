<?php
/**
 * @author Omid AMINI
 * @link https://www.linkedin.com/in/omid-amini/
 * @license OSL 3.0
 */
declare(strict_types=1);
namespace Blueprint\Module\Psmoduleblueprint\Factory;

use Context;
use Module;

class HookFactory
{
    /**
     * @var \Module
     */
    private Module $module;
    /**
     * @var \Context
     */
    private Context $context;
    /**
     * @var array
     */
    private array $additionalParams = [];
    /**
     * @param \Module $module
     * @param \Context $context
     * @param array $additionalParams
     */
    public function __construct(Module $module, Context $context, ...$additionalParams)
    {
        $this->module = $module;
        $this->context = $context;
        $this->additionalParams = $additionalParams;
    }
    public function create(string $hookClassName): object
    {
        if(!class_exists($hookClassName)) {
            throw new \InvalidArgumentException('Hook class does not exist: ' . $hookClassName);
        }
        return new $hookClassName($this->module, $this->context, ...$this->additionalParams);
    }
}