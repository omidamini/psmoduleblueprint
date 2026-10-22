<?php
/**
 * @author Omid AMINI
 * @link https://www.linkedin.com/in/omid-amini/
 * @license OSL 3.0
 */
declare(strict_types=1);

namespace Blueprint\Module\Psmoduleblueprint\Service;

use Blueprint\Module\Psmoduleblueprint\Config\ModuleConfig;
use Blueprint\Module\Psmoduleblueprint\Interface\EntityInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

/**
 * Repository limited to entities that belong to this module.
 */
class ModuleEntityService implements EntityInterface
{
    private string $entity;

    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function set(string $entity): self
    {
        $prefix = ModuleConfig::MODULE_NAME_SPACE . '\\Entity\\';
        if (!str_starts_with($entity, $prefix) || !class_exists($entity)) {
            throw new \InvalidArgumentException('This entity class is not allowed.');
        }

        $this->entity = $entity;

        return $this;
    }

    public function find(int $id): ?object
    {
        $entity = $this->repository()->find($id);

        return is_object($entity) ? $entity : null;
    }

    public function findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null): array
    {
        return $this->repository()->findBy($criteria, $orderBy, $limit, $offset);
    }

    public function findOneBy(array $criteria): ?object
    {
        $entity = $this->repository()->findOneBy($criteria);

        return is_object($entity) ? $entity : null;
    }

    public function findAll(): array
    {
        return $this->repository()->findAll();
    }

    public function save(object $entity): void
    {
        $this->assertModuleEntity($entity);
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    public function remove(object $entity): void
    {
        $this->assertModuleEntity($entity);
        $this->entityManager->remove($entity);
        $this->entityManager->flush();
    }

    public function getRepository(): EntityRepository
    {
        return $this->repository();
    }

    private function repository(): EntityRepository
    {
        if (!isset($this->entity)) {
            throw new \LogicException('Select an entity with set() before querying it.');
        }

        return $this->entityManager->getRepository($this->entity);
    }

    private function assertModuleEntity(object $entity): void
    {
        $prefix = ModuleConfig::MODULE_NAME_SPACE . '\\Entity\\';
        if (!str_starts_with($entity::class, $prefix)) {
            throw new \InvalidArgumentException('Refusing to modify an object outside the module.');
        }
    }
}
