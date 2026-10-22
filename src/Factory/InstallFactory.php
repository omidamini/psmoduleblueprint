<?php
/**
 * @author Omid AMINI
 * @link https://www.linkedin.com/in/omid-amini/
 * @license OSL 3.0
 */
declare(strict_types=1);

namespace Blueprint\Module\Psmoduleblueprint\Factory;

use Blueprint\Module\Psmoduleblueprint\Install\Database\QueryManager;
use Blueprint\Module\Psmoduleblueprint\Install\Hook\HookManager;
use Blueprint\Module\Psmoduleblueprint\Install\Hook\HooksList;
use Blueprint\Module\Psmoduleblueprint\Install\Installer;
use Blueprint\Module\Psmoduleblueprint\Install\Tabmenu\MenusList;
use Blueprint\Module\Psmoduleblueprint\Install\Tabmenu\TabMenuManager;
use Doctrine\ORM\EntityManagerInterface;
use Module;

/**
 * Composition root for the installer and its collaborators.
 */
class InstallFactory
{
    public function __construct(private Module $module)
    {
    }

    public function create(): Installer
    {
        $entityManager = $this->module->get('doctrine.orm.entity_manager');
        if (!$entityManager instanceof EntityManagerInterface) {
            throw new \RuntimeException('Doctrine entity manager is not available.');
        }

        return new Installer(
            $this->module,
            new HookManager($this->module, new HooksList()),
            new QueryManager(),
            new TabMenuManager($this->module->name, new MenusList(), $entityManager)
        );
    }
}
