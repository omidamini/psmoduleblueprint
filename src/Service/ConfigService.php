<?php
/**
 * @author Omid AMINI
 * @link https://www.linkedin.com/in/omid-amini/
 * @license OSL 3.0
 * @copyright 2026 ITIS COMMERCE (https://www.itis-commerce.com/)
 */
namespace Blueprint\Module\Psmoduleblueprint\Service;

use Doctrine\ORM\EntityManagerInterface;
use Blueprint\Module\Psmoduleblueprint\Entity\PsmoduleblueprintConf;

class ConfigService
{
    public function __construct(private EntityManagerInterface $entityManager) {}
    /**
     * Get configuration value by name.
     *
     * @param string $name
     * @return string|null
     */
    public function get(string $name): ?string
    {
        $config = $this->entityManager->getRepository(PsmoduleblueprintConf::class)
            ->findOneBy(['name' => $name]);
        return $config ? $config->getValue() : null;
    }
    /**
     * Set configuration value by name.
     *
     * @param string $name
     * @param string $value
     *
     * @return void
     */
    public function set(string $name, string $value): void
    {
        $config = $this->entityManager->getRepository(PsmoduleblueprintConf::class)
            ->findOneBy(['name' => $name]);
        if (!$config) {
            $config = new PsmoduleblueprintConf();
            $config->setName($name);
        }
        $config->setValue($value);
        $this->entityManager->persist($config);
        $this->entityManager->flush();
    }
    /**
     * Remove configuration by name.
     *
     * @param string $name
     * @return void
     */
    public function remove(string $name): void
    {
        $config = $this->entityManager->getRepository(PsmoduleblueprintConf::class)
            ->findOneBy(['name' => $name]);
        if ($config) {
            $this->entityManager->remove($config);
            $this->entityManager->flush();
        }
    }
}