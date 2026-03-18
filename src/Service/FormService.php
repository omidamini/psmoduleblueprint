<?php
/**
 * @author Omid AMINI
 * @link https://www.linkedin.com/in/omid-amini/
 * @license OSL 3.0
 */
namespace Blueprint\Module\Psmoduleblueprint\Service;

use Doctrine\ORM\EntityManagerInterface;
use HTMLPurifier;
use Blueprint\Module\Psmoduleblueprint\Config\ModuleConfig;
use Blueprint\Module\Psmoduleblueprint\Config\ModuleMessage;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tools;

class FormService
{
    private array $options = []; 
    private $data;
    private $handleFormSubmission;
    private $form;
    public function __construct(
        private RequestStack $requestStack,
        private EntityManagerInterface $entityManager,
        private FormFactory $formFactory,
        private Session $session,
        private RouterInterface $router,
        private TranslatorInterface $translator,
    ) 
    {

    }

    public function createForm(string $formClass, $data = null, $redirectUrl = null, bool $handleFormSubmission = true): FormView
    {
        $this->handleFormSubmission = $handleFormSubmission;
        $this->form = $this->formFactory->create($formClass, $data, $this->getOptions());
        $this->form->handleRequest($this->requestStack->getCurrentRequest());
        if ($this->isFormValid($this->form)) {
            $this->handleFormSubmission($this->form);
            if ($redirectUrl) {
                $this->redirect($redirectUrl);
            }
        }
        return $this->form->createView();
    }
    public function handleFormSubmission(FormInterface $form): void
    {
        if(!$this->handleFormSubmission) {
            $this->setData($form->getData());
            return; // Ne pas traiter la soumission si handleFormSubmission est faux
        }
        $data = $form->getData();
        // Purify HTML inputs
        foreach ($form->all() as $field) {
            if ($field->getConfig()->getType()->getBlockPrefix() === 'textarea') {
                $input = $field->getData();
                if (is_string($input)) {
                    $input = $this->purifyHtml($input);
                    $setMethod = 'set' . ucfirst($field->getName());
                    if (method_exists($data, $setMethod)) {
                        $data->$setMethod($input);
                    }
                }
            }
        }
        // Handle file upload if needed
        $files = array_filter($form->all(), function ($field) {
            return $field->getConfig()->getType()->getBlockPrefix() === 'file';
        });
        $files = array_keys($files);
        foreach ($files as $file_name) {
            $file = $form->get($file_name)->getData();
            if ($file) {
                $fileName = uniqid() . '.' . $file->guessExtension();
                $file->move(ModuleConfig::IMG_DIR, $fileName);
                $setMethod = 'set' . ucfirst($file_name);
                $data->$setMethod($fileName);
            }
        }
        try {
            $this->entityManager->persist($data);
            $this->entityManager->flush();
            $this->setData($data);
            $this->addFlashMessage('success', $this->translator->trans(ModuleMessage::SAVE_SUCCESS,[], 'Modules.Psmoduleblueprint.Message'));
        } catch (\Exception $e) {
            throw new \RuntimeException('Failed to save configuration: ' . $e->getMessage());
        }
    }
    public function setOptions(array $options): self
    {
        $this->options = $options;
        return $this;
    }
    public function getOptions(): ?array
    {
        return $this->options;
    }
    public function isFormValid($form)
    {
        return $form->isSubmitted() && $form->isValid();
    }
    private function redirect($url): void
    {
        if (PHP_SAPI === 'cli') {
            return; // Ne pas rediriger en contexte CLI/test
        }
        header('Location: ' . $url);
        exit();
    }
    public function addFlashMessage(string $type, string $message): void 
    {
        $flashBag = $this->session->getFlashBag();
        $flashBag->add($type, $message);
    }
    public function getData()
    {
        return $this->data;
    }
    public function setData($data): self
    {
        $this->data = $data;
        return $this;
    }
    private function purifyHtml(string $input): string
    {
        // Utilisez une bibliothèque de purification HTML ici, par exemple HTMLPurifier
        $purifier = new HTMLPurifier();
        $purified = $purifier->purify($input);
        //$purified = Tools::purifyHTML($input);
        return $purified;
    }
    public function getForm(): FormInterface
    {
        return $this->form;
    }
}