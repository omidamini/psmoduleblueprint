<?php
/**
 * @author Omid AMINI
 * @link https://www.linkedin.com/in/omid-amini/
 * @license OSL 3.0
 */
declare(strict_types=1);

namespace Blueprint\Module\Psmoduleblueprint\Hook;

use Context;
use Module;

abstract class Hook
{
    protected Module $module;
    protected Context $context;
    protected array $params = [];

    public function __construct(Module $module, Context $context)
    {
        $this->module = $module;
        $this->context = $context;
    }

    abstract public function run();

    public function setParams(array $params): self
    {
        $this->params = $params;

        return $this;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    public function render(string $template, array $params = []): string
    {
        $this->assertTwigTemplate($template);
        $twig = $this->module->get('twig');
        if (!is_object($twig) || !method_exists($twig, 'render')) {
            throw new \RuntimeException('Twig is not available.');
        }

        return $twig->render($template, $params);
    }

    public function display(string $template, array $params = []): string
    {
        $this->assertTemplate($template);

        if (str_ends_with($template, '.html.twig')) {
            return $this->render(
                '@Modules/' . $this->module->name . '/views/templates/hook/' . $template,
                $params
            );
        }

        $this->context->smarty->assign($params);

        return $this->module->display(
            $this->module->getLocalPath() . $this->module->name . '.php',
            $template
        );
    }

    private function assertTemplate(string $template): void
    {
        if (!preg_match('/^[A-Za-z0-9_-]+\.(tpl|html\.twig)$/', $template)) {
            throw new \InvalidArgumentException('Invalid template name.');
        }
    }

    private function assertTwigTemplate(string $template): void
    {
        $prefix = '@Modules/' . $this->module->name . '/views/templates/';
        $relative = str_starts_with($template, $prefix) ? substr($template, strlen($prefix)) : '';

        if ($relative === '' || str_contains($relative, '..') || !preg_match('/^[A-Za-z0-9_\/-]+\.html\.twig$/', $relative)) {
            throw new \InvalidArgumentException('Invalid template name.');
        }
    }
}
