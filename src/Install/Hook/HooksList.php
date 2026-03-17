<?php
/**
 * @author Omid AMINI
 * @link https://www.linkedin.com/in/omid-amini/
 * @license OSL 3.0
 * @copyright 2026 ITIS COMMERCE (https://www.itis-commerce.com/)
 */
namespace Blueprint\Module\Psmoduleblueprint\Install\Hook;
class HooksList
{
    /**
     * @var array
     */
    private array $hooks = [
        'actionFrontControllerSetMedia',
    ];

    /**
     * @return array
     */
    public function getHooks(): array
    {
        return $this->hooks;
    }
}