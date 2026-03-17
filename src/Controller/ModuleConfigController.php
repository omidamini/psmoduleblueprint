<?php
/**
 * @author Omid AMINI
 * @link https://www.linkedin.com/in/omid-amini/
 * @license OSL 3.0
 * @copyright 2026 ITIS COMMERCE (https://www.itis-commerce.com/)
 */
declare(strict_types=1);
namespace Blueprint\Module\Psmoduleblueprint\Controller;

use Blueprint\Module\Psmoduleblueprint\Config\ModuleConfig;
use Blueprint\Module\Psmoduleblueprint\Service\FormService;
use Blueprint\Module\Psmoduleblueprint\Service\ModuleEntityService;
use Blueprint\Module\Psmoduleblueprint\Form\ModuleSearchForm;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;
class ModuleConfigController extends FrameworkBundleAdminController
{
    public function __construct(private RequestStack $requestStack, private FormService $formService, private ModuleEntityService $moduleEntityService, private TranslatorInterface $translator)
    {
    }
     public function configureAction(): Response
    {
        $searchForm = $this->createForm(ModuleSearchForm::class, null, [
            #'action' => $this->generateUrl('itis_customerservice_sparepart', ['id' => $id])
        ]);
        $welcome_msg = 'Hello PrestaShop!';
        return $this->render('@Modules/psmoduleblueprint/views/templates/admin/config/index.html.twig', [
            'searchForm' => $searchForm->createView(),
            'welcome_msg' => $welcome_msg,
        ]);
    }
}