<?php
/**
 * @author Omid AMINI
 * @link https://www.linkedin.com/in/omid-amini/
 * @license OSL 3.0
 */
declare(strict_types=1);
namespace Blueprint\Module\Psmoduleblueprint\Hook;
use Blueprint\Module\Psmoduleblueprint\Hook\Hook;
class ActionFrontControllerSetMediaHook extends Hook
{
    /**
     * Execute the hook.
     *
     * @param array $params
     * @return bool
     */
    public function run()
    {
         //add custom css and js files using media with prirority
        $this->context->controller->registerStylesheet(
            "module-{$this->module->name}-style",
            'modules/' . $this->module->name . '/views/css/front.css',
            ['media' => 'all', 'priority' => 200]
        );
        $this->context->controller->registerJavascript(
            "module-{$this->module->name}-script",
            'modules/' . $this->module->name . '/views/js/front.js',
            ['position' => 'bottom', 'priority' => 200]
        );
    }
}