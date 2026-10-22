<?php
/**
 * @author Omid AMINI
 * @link https://www.linkedin.com/in/omid-amini/
 * @license OSL 3.0
 */
declare(strict_types=1);

namespace Blueprint\Module\Psmoduleblueprint\Service;

use Blueprint\Module\Psmoduleblueprint\Entity\PsmoduleblueprintConf;
use Doctrine\ORM\EntityManagerInterface;

class ConfigService
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function get(string $name): ?string
    {
        $this->assertName($name);
        $config = $this->entityManager->getRepository(PsmoduleblueprintConf::class)->findOneBy(['name' => $name]);

        return $config instanceof PsmoduleblueprintConf ? $config->getValue() : null;
    }

    public function set(string $name, string $value): void
    {
        $this->assertName($name);
        $this->assertValue($value);

        $config = $this->entityManager->getRepository(PsmoduleblueprintConf::class)->findOneBy(['name' => $name]);
        if (!$config instanceof PsmoduleblueprintConf) {
            $config = new PsmoduleblueprintConf();
            $config->setName($name);
        }

        $config->setValue($value);
        $this->entityManager->persist($config);
        $this->entityManager->flush();
    }

    public function remove(string $name): void
    {
        $this->assertName($name);
        $config = $this->entityManager->getRepository(PsmoduleblueprintConf::class)->findOneBy(['name' => $name]);
        if ($config instanceof PsmoduleblueprintConf) {
            $this->entityManager->remove($config);
            $this->entityManager->flush();
        }
    }

    private function assertName(string $name): void
    {
        if (!preg_match('/^[A-Za-z0-9_]{1,64}$/', $name)) {
            throw new \InvalidArgumentException('Invalid configuration name.');
        }
    }

    private function assertValue(string $value): void
    {
        if (strlen($value) > 255 || str_contains($value, "\0")) {
            throw new \InvalidArgumentException('Invalid configuration value.');
        }
    }
}
