<?php
/**
 * @author Omid AMINI
 * @link https://www.linkedin.com/in/omid-amini/
 * @license OSL 3.0
 * @copyright 2026 ITIS COMMERCE (https://www.itis-commerce.com/)
 */
declare(strict_types=1);
namespace Blueprint\Module\Psmoduleblueprint\Hook;
/**
 * Class Hook
 * @package Blueprint\Module\Services\Hook
 *
 * This class serves as a base for all hooks.
 */
use Module;
use Context;
class Hook {
    /**
     * @var Module
     */
    protected $module;
    /**
     * @var Context
     */
    protected $context;
    /**
     * @var array
     */
    protected $params = [];
    /**
     * Hook constructor.
     *
     * @param Module $module
     * @param Context $context
     */
    public function __construct(Module $module , Context $context) {
        $this->module = $module;
        $this->context = $context;
    }
    /**
     * Set parameters for the hook.
     * @param array $params
     * @return $this
     */
    public function setParams(array $params): self {
        $this->params = $params;
        return $this;
        
    }
    /**
     * Get parameters for the hook.
     * @return array
     */
    public function getParams(): array {
        return $this->params;
    }
     /**
     * Render a Twig template
     *
     * @param string $template
     * @param array $params
     * @return string
     */
    public function render(string $template, array $params = []): string
    {
        $twig = $this->module->get('twig'); // Récupérer le service Twig depuis le conteneur
        return $twig->render($template, $params);
    }
    /**
     * Display template from MODULE/views/templates/hook/
     *
     * @param string $template
     * @return string
     */ 
    public function display(string $template, array $params = [])
    {
        
        if(strpos($template, '.html.twig') == true){
           return $this->render('@Modules/'.$this->module->name.'/views/templates/hook/' . $template,$params);
        }
        $this->context->smarty->assign($params);
        return $this->module->display(
            $this->module->getLocalPath() . $this->module->name.".php",
            '/views/templates/hook/' . $template
        );
    }
}