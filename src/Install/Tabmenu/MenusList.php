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
     * example: 'AdminModuleClassName' => ['class_name' => 'AdminModuleClassName', 'name' => 'tab name', 'parent_class_name' => 'CONFIGURE']
     */
    private $menus = [
        'AdminPsmoduleblueprintConfig' => [
            'class_name' => 'AdminPsmoduleblueprintConfig',
            'module_name' => 'psmoduleblueprint',
            'name' => 'ps module blueprint',
            'parent_class_name' => 'CONFIGURE',
        ],
    ];

    /**
     * @return array
     */
    public function getMenus(): array
    {
        return $this->menus;
    }
}
