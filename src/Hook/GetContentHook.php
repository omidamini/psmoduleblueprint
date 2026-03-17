<?php
/**
 * @author Omid AMINI
 * @link https://www.linkedin.com/in/omid-amini/
 * @license OSL 3.0
 * @copyright 2026 ITIS COMMERCE (https://www.itis-commerce.com/)
 */
declare(strict_types=1);
namespace Blueprint\Module\Psmoduleblueprint\Hook;
use PrestaShop\PrestaShop\Adapter\SymfonyContainer;
use Blueprint\Module\Psmoduleblueprint\Hook\Hook;
use Tools;

class GetContentHook extends Hook
{
    /**
     * Execute the hook.
     *
     * @param array $params
     * @return bool
     */
    public function run()
    {
        $sfContainer = SymfonyContainer::getInstance();
        $router = $sfContainer->get('router');
        $url = $router->generate('psmoduleblueprint_configure');
        Tools::redirectAdmin($url);
    }
}