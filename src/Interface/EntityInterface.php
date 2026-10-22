<?php
/**
 * @author Omid AMINI
 * @link https://www.linkedin.com/in/omid-amini/
 * @license OSL 3.0
 */
declare(strict_types=1);

namespace Blueprint\Module\Psmoduleblueprint\Interface;

interface EntityInterface
{
    public function set(string $entityClass): self;

    public function find(int $id): ?object;

    public function findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null): array;

    public function findOneBy(array $criteria): ?object;

    public function findAll(): array;

    public function save(object $entity): void;

    public function remove(object $entity): void;
}
