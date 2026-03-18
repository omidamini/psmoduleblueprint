<?php
/**
 * @author Omid AMINI
 * @link https://www.linkedin.com/in/omid-amini/
 * @license OSL 3.0
 */
declare(strict_types=1);
namespace Blueprint\Module\Psmoduleblueprint\Service;

use Doctrine\ORM\EntityManagerInterface;
use Blueprint\Module\Psmoduleblueprint\Interface\EntityInterface;

class ModuleEntityService implements EntityInterface
{
    
    private string $entity;
    
    public function __construct(private EntityManagerInterface $entityManager) {}
   
    public function set(string $entity):self
    {
        if (!class_exists($entity)) {
            throw new \InvalidArgumentException(sprintf('Class %s does not exist.', $entity));
        }
        $this->entity = $entity;
        return $this;
    }
    public function find(int $id): ?object
    {
        return $this->entityManager->getRepository($this->entity)->find($id);
    }
    public function findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null): array
    {
        return $this->entityManager->getRepository($this->entity)->findBy($criteria, $orderBy, $limit, $offset);
    }
    public function findOneBy(array $criteria): ?object
    {
        return $this->entityManager->getRepository($this->entity)->findOneBy($criteria);
    }
    public function findAll(?string $entity =null): array
    {
        if ($entity) {
            $this->set($entity);
        }
        return $this->entityManager->getRepository($this->entity)->findAll();
    }
     public function save($entity):void
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }
    public function remove($entity):void
    {
        $this->entityManager->remove($entity);
        $this->entityManager->flush();
    }
    public function getRepository()
    {
        return $this->entityManager->getRepository($this->entity);
    }
}