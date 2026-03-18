<?php
/**
 * @author Omid AMINI
 * @link https://www.linkedin.com/in/omid-amini/
 * @license OSL 3.0
 */
declare(strict_types=1);
namespace Blueprint\Module\Psmoduleblueprint\Install\Tabmenu;

use Language;
use PrestaShop\PrestaShop\Adapter\SymfonyContainer;
use PrestaShopBundle\Entity\Tab as EntityTab;
use Tab;
class TabMenuManger {
    private array $menusList;
    public function __construct(
        private string $moduleName, 
        MenusList $menusList,
        private $entityManager
    ){
        if ($this->entityManager === null) {
            throw new \RuntimeException('EntityManager is required');
        }
        $this->menusList = $menusList->getMenus();
    }
    public function registerMenus()
    {
        foreach ($this->menusList as $menu) {
            $tabId = $this->entityManager->getRepository(EntityTab::class)->findOneIdByClassName($menu['parent_class_name']);
            $tab = new Tab();
            $tab->class_name = $menu['class_name'];
            $tab->module = $this->moduleName;
            $tab->name = array_fill_keys(Language::getIDs(), $menu['name']);
            $tab->id_parent = $tabId??0; // If parent tab does not exist, set to 0
            $tab->add();
        }
        return true;
    }
    public function unregisterMenus(): bool
    {
        
        foreach ($this->menusList as $menu)
        {
            $tabId = (int)$this->entityManager->getRepository(EntityTab::class)->findOneIdByClassName($menu['class_name']);
            if ($tabId) {
                $tab = new Tab($tabId);
                $tab->delete();
            }
        }
        return true;
    }
     public static function getInstance(string $moduleName, string $menusListClass): TabMenuManger
    {
        
        $sfContainer = SymfonyContainer::getInstance();
        $entityManager = $sfContainer->get('doctrine.orm.entity_manager');
        return new self($moduleName, new $menusListClass(), $entityManager);
    }
    
}