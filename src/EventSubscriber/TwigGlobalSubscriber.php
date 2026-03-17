<?php
/**
 * @author Omid AMINI
 * @link https://www.linkedin.com/in/omid-amini/
 * @license OSL 3.0
 * @copyright 2026 ITIS COMMERCE (https://www.itis-commerce.com/)
 */ 
namespace Blueprint\Module\Psmoduleblueprint\EventSubscriber;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Language;
use Context;
use Blueprint\Module\Psmoduleblueprint\Entity\PsmoduleblueprintConf;
use Blueprint\Module\Psmoduleblueprint\Entity\Lang;
use Blueprint\Module\Psmoduleblueprint\Entity\Shop;

class TwigGlobalSubscriber implements EventSubscriberInterface
{
    private $twig;
    private $entityManager;

    public function __construct(\Twig\Environment $twig, \Doctrine\ORM\EntityManagerInterface $entityManager)
    {
        $this->twig = $twig;
        $this->entityManager = $entityManager;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }

    public function onKernelController()
    {
        $context = Context::getContext();
        $currentLanguageConf = $this->entityManager->getRepository(PsmoduleblueprintConf::class)
            ->findOneBy(['name' => 'current_language']);
        if(!$currentLanguageConf) {
            $psmoduleblueprintConf = new PsmoduleblueprintConf();
            $psmoduleblueprintConf->setName('current_language');
            $psmoduleblueprintConf->setValue($context->language->id);
            $this->entityManager->persist($psmoduleblueprintConf);
            $this->entityManager->flush();
        }
        $currentLanguage = $this->entityManager->getRepository(Lang::class)
            ->find($currentLanguageConf->getValue());
        $currentShopConf = $this->entityManager->getRepository(PsmoduleblueprintConf::class)
            ->findOneBy(['name' => 'current_shop']);
        if(!$currentShopConf) {
            $psmoduleblueprintConf = new PsmoduleblueprintConf();
            $psmoduleblueprintConf->setName('current_shop');
            $psmoduleblueprintConf->setValue($context->shop->id);
            $this->entityManager->persist($psmoduleblueprintConf);
            $this->entityManager->flush();
        }
        $currentShop = $this->entityManager->getRepository(Shop::class)
            ->find($currentShopConf->getValue());
        
        
        $languages = Language::getLanguages(true, $context->shop->id);
        $shops = $context->shop->getShops();
        $this->twig->addGlobal('psmoduleblueprint', [
            'languages' => $languages,
            'shops' => $shops,
            'current_language' => $currentLanguage,
            'current_shop' => $currentShop,
        ]);
    }
}