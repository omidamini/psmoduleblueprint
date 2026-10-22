<?php
/**
 * @author Omid AMINI
 * @link https://www.linkedin.com/in/omid-amini/
 * @license OSL 3.0
 */
declare(strict_types=1);

namespace Blueprint\Module\Psmoduleblueprint\Hook;

class ActionFrontControllerSetMediaHook extends Hook
{
    public function run(): void
    {
        $this->registerAsset(
            'style',
            'views/css/front.css',
            static fn ($controller, string $id, string $path) => $controller->registerStylesheet(
                $id,
                $path,
                ['media' => 'all', 'priority' => 200]
            )
        );
        $this->registerAsset(
            'script',
            'views/js/front.js',
            static fn ($controller, string $id, string $path) => $controller->registerJavascript(
                $id,
                $path,
                ['position' => 'bottom', 'priority' => 200]
            )
        );
    }

    private function registerAsset(string $kind, string $relativePath, callable $register): void
    {
        if (!is_file($this->module->getLocalPath() . $relativePath)) {
            return;
        }

        $controller = $this->context->controller;
        if (!is_object($controller)) {
            return;
        }

        $register(
            $controller,
            'module-' . $this->module->name . '-' . $kind,
            'modules/' . $this->module->name . '/' . $relativePath
        );
    }
}
