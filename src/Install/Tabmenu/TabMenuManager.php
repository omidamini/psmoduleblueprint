<?php
/**
 * @author Omid AMINI
 * @link https://www.linkedin.com/in/omid-amini/
 * @license OSL 3.0
 */
declare(strict_types=1);

namespace Blueprint\Module\Psmoduleblueprint\Install\Tabmenu;

use Doctrine\ORM\EntityManagerInterface;
use Language;
use PrestaShopBundle\Entity\Tab as EntityTab;
use Tab;

class TabMenuManager
{
    private array $menusList;

    public function __construct(
        private string $moduleName,
        MenusList $menusList,
        private EntityManagerInterface $entityManager,
    ) {
        if (!preg_match('/^[a-z0-9_]+$/', $moduleName)) {
            throw new \InvalidArgumentException('Invalid module name.');
        }

        $this->menusList = $menusList->getMenus();
    }

    public function registerMenus(): bool
    {
        foreach ($this->menusList as $menu) {
            $className = (string) ($menu['class_name'] ?? '');
            $parentClassName = (string) ($menu['parent_class_name'] ?? '');
            $name = (string) ($menu['name'] ?? '');

            if (!preg_match('/^[A-Za-z0-9_]+$/', $className) || !preg_match('/^[A-Za-z0-9_]+$/', $parentClassName)) {
                throw new \InvalidArgumentException('Invalid admin tab name.');
            }
            if ($name === '' || strlen($name) > 128) {
                throw new \InvalidArgumentException('Invalid admin tab label.');
            }

            $tabId = (int) $this->entityManager->getRepository(EntityTab::class)->findOneIdByClassName($parentClassName);
            if ($tabId <= 0) {
                throw new \RuntimeException('Parent admin tab was not found.');
            }

            $tab = new Tab();
            $tab->class_name = $className;
            $tab->module = $this->moduleName;
            $languageIds = Language::getIDs(false);
            if (!is_array($languageIds) || $languageIds === []) {
                throw new \RuntimeException('No language is available for the admin tab.');
            }

            $tab->name = array_fill_keys($languageIds, $name);
            $tab->id_parent = $tabId;
            $tab->active = 1;
            if (!$tab->add()) {
                throw new \RuntimeException('Failed to register the admin tab.');
            }
        }

        return true;
    }

    public function unregisterMenus(): bool
    {
        foreach ($this->menusList as $menu) {
            $className = (string) ($menu['class_name'] ?? '');
            if (!preg_match('/^[A-Za-z0-9_]+$/', $className)) {
                continue;
            }

            $tabId = (int) $this->entityManager->getRepository(EntityTab::class)->findOneIdByClassName($className);
            if ($tabId > 0) {
                $tab = new Tab($tabId);
                $tab->delete();
            }
        }

        return true;
    }
}
