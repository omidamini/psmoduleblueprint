<?php
/**
 * @author Omid AMINI
 * @link https://www.linkedin.com/in/omid-amini/
 * @license OSL 3.0
 */
declare(strict_types=1);

namespace Blueprint\Module\Psmoduleblueprint\Service;

use Blueprint\Module\Psmoduleblueprint\Config\ModuleConfig;
use Blueprint\Module\Psmoduleblueprint\Config\ModuleMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tools;

class FormService
{
    private array $options = [];
    private mixed $data = null;
    private bool $handleFormSubmission = true;
    private ?FormInterface $form = null;

    public function __construct(
        private RequestStack $requestStack,
        private EntityManagerInterface $entityManager,
        private FormFactoryInterface $formFactory,
        private TranslatorInterface $translator,
        private HtmlSanitizer $htmlSanitizer,
        private SecureFileUploader $fileUploader,
        private LoggerService $logger,
    ) {
    }

    public function createForm(string $formClass, mixed $data = null, ?string $redirectUrl = null, bool $handleFormSubmission = true): FormView
    {
        $this->handleFormSubmission = $handleFormSubmission;
        $this->form = $this->formFactory->create($formClass, $data, $this->options);
        $this->form->handleRequest($this->requestStack->getCurrentRequest());

        if ($this->isFormValid($this->form)) {
            $this->handleFormSubmission($this->form);
            if ($redirectUrl !== null) {
                $this->redirect($redirectUrl);
            }
        }

        return $this->form->createView();
    }

    public function handleFormSubmission(FormInterface $form): void
    {
        $data = $form->getData();
        if (!$this->handleFormSubmission) {
            $this->data = $data;

            return;
        }

        if (!is_object($data)) {
            throw new \InvalidArgumentException('The form is not bound to an entity.');
        }

        $this->assertModuleEntity($data);

        try {
            $this->purifyTextareas($form, $data);
            $this->storeUploadedFiles($form, $data);
            $this->entityManager->persist($data);
            $this->entityManager->flush();
            $this->data = $data;
            $this->addFlashMessage(
                'success',
                $this->translator->trans(ModuleMessage::SAVE_SUCCESS, [], 'Modules.Psmoduleblueprint.Message')
            );
        } catch (\Throwable $exception) {
            $this->logger->error('Failed to save configuration: ' . $exception->getMessage());
            $this->addFlashMessage(
                'error',
                $this->translator->trans(ModuleMessage::ERROR, [], 'Modules.Psmoduleblueprint.Message')
            );
        }
    }

    public function setOptions(array $options): self
    {
        $this->options = $options;

        return $this;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function isFormValid(FormInterface $form): bool
    {
        return $form->isSubmitted() && $form->isValid();
    }

    public function addFlashMessage(string $type, string $message): void
    {
        if (!preg_match('/^[a-z]+$/', $type)) {
            $type = 'error';
        }

        $request = $this->requestStack->getCurrentRequest();
        if ($request === null || !$request->hasSession()) {
            return;
        }

        $request->getSession()->getFlashBag()->add($type, $message);
    }

    public function getData(): mixed
    {
        return $this->data;
    }

    public function getForm(): FormInterface
    {
        if (!$this->form instanceof FormInterface) {
            throw new \LogicException('Create the form before reading it.');
        }

        return $this->form;
    }

    private function purifyTextareas(FormInterface $form, object $data): void
    {
        foreach ($form->all() as $field) {
            if ($field->getConfig()->getType()->getInnerType()->getBlockPrefix() !== 'textarea') {
                continue;
            }

            $input = $field->getData();
            if (!is_string($input)) {
                continue;
            }

            $this->applySetter($data, $field->getName(), $this->htmlSanitizer->purify($input));
        }
    }

    private function storeUploadedFiles(FormInterface $form, object $data): void
    {
        foreach ($form->all() as $field) {
            if ($field->getConfig()->getType()->getInnerType()->getBlockPrefix() !== 'file') {
                continue;
            }

            $file = $field->getData();
            if (!$file instanceof UploadedFile) {
                continue;
            }

            $this->applySetter($data, $field->getName(), $this->fileUploader->store($file));
        }
    }

    private function applySetter(object $data, string $field, mixed $value): void
    {
        if (!preg_match('/^[A-Za-z][A-Za-z0-9_]*$/', $field)) {
            throw new \InvalidArgumentException('Invalid form field name.');
        }

        $setter = 'set' . ucfirst($field);
        if (!method_exists($data, $setter)) {
            throw new \InvalidArgumentException('The entity does not accept this field.');
        }

        $data->{$setter}($value);
    }

    private function assertModuleEntity(object $data): void
    {
        $prefix = ModuleConfig::MODULE_NAME_SPACE . '\\Entity\\';
        if (!str_starts_with($data::class, $prefix)) {
            throw new \InvalidArgumentException('Refusing to persist an object outside the module.');
        }
    }

    private function redirect(string $url): void
    {
        if (PHP_SAPI === 'cli' || !$this->isAllowedRedirect($url)) {
            throw new \InvalidArgumentException('Refusing to redirect to this URL.');
        }

        (new RedirectResponse($url))->send();
        exit;
    }

    private function isAllowedRedirect(string $url): bool
    {
        if ($url === '' || preg_match('/[\r\n\x00\\\\]/', $url) === 1) {
            return false;
        }

        if (str_starts_with($url, '/') && !str_starts_with($url, '//') && !str_starts_with($url, '/\\')) {
            return true;
        }

        $parts = parse_url($url);
        if (!is_array($parts) || empty($parts['host']) || isset($parts['user']) || isset($parts['pass'])) {
            return false;
        }

        $scheme = strtolower((string) ($parts['scheme'] ?? ''));
        if (!in_array($scheme, ['http', 'https'], true)) {
            return false;
        }

        $allowedHosts = array_filter([
            strtolower((string) Tools::getShopDomain(false)),
            strtolower((string) Tools::getShopDomainSsl(false)),
        ]);

        return in_array(strtolower((string) $parts['host']), $allowedHosts, true);
    }
}
