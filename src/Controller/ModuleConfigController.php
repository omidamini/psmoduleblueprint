<?php
/**
 * @author Omid AMINI
 * @link https://www.linkedin.com/in/omid-amini/
 * @license OSL 3.0
 */
declare(strict_types=1);

namespace Blueprint\Module\Psmoduleblueprint\Controller;

use Blueprint\Module\Psmoduleblueprint\Form\ModuleSearchForm;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ModuleConfigController extends PrestaShopAdminController
{
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))", message: 'Access denied.', redirectRoute: 'admin_homepage')]
    public function configureAction(Request $request): Response
    {
        $searchForm = $this->createForm(ModuleSearchForm::class);
        $searchForm->handleRequest($request);
        $searchQuery = '';

        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $data = $searchForm->getData();
            $searchQuery = $this->sanitizeSearchQuery(is_array($data) ? (string) ($data['q'] ?? '') : '');
        }

        return $this->render('@Modules/psmoduleblueprint/views/templates/admin/config/index.html.twig', [
            'searchForm' => $searchForm->createView(),
            'welcome_msg' => 'Hello PrestaShop!',
            'search_query' => $searchQuery,
        ]);
    }

    private function sanitizeSearchQuery(string $query): string
    {
        $query = trim(str_replace("\0", '', $query));
        $query = preg_replace('/[^\p{L}\p{N} ._-]/u', '', $query) ?? '';

        return substr($query, 0, 64);
    }
}
