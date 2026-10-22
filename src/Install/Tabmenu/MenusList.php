<?php
/**
 * @author Omid AMINI
 * @link https://www.linkedin.com/in/omid-amini/
 * @license OSL 3.0
 */
declare(strict_types=1);
namespace Blueprint\Module\Psmoduleblueprint\Install\Tabmenu;
class MenusList
{
    /**
     * @var array
     * example: 'AdminModuleName' => ['class_name' => 'AdminModuleName', 'name' => 'tab name', 'parent_class_name' => 'CONFIGURE']
     */
    private $menus = ['AdminPsmoduleblueprintConfig' => [
        'class_name' => 'AdminPsmoduleblueprintConfig', // The class name of the tab
        'module_name' => 'psmoduleblueprint', // The module name
        'name' => 'ps module blueprint', // The name of the tab
        'parent_class_name' => 'CONFIGURE', // Parent tab class name. Install fails if this tab does not exist.
    ]];

    /**
     * @return array
     */
    public function getMenus(): array
    {
        return $this->menus;
    }
}