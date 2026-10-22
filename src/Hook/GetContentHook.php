<?php
/**
 * @author Omid AMINI
 * @link https://www.linkedin.com/in/omid-amini/
 * @license OSL 3.0
 */
declare(strict_types=1);

namespace Blueprint\Module\Psmoduleblueprint\Hook;

use PrestaShop\PrestaShop\Adapter\SymfonyContainer;
use Tools;

class GetContentHook extends Hook
{
    public function run(): void
    {
        $container = SymfonyContainer::getInstance();
        if ($container === null || !$container->has('router')) {
            throw new \RuntimeException('The configuration page is not available.');
        }

        Tools::redirectAdmin($container->get('router')->generate('psmoduleblueprint_configure'));
    }
}
