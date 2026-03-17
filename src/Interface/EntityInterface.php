<?php
/**
 * @author Omid AMINI
 * @link https://www.linkedin.com/in/omid-amini/
 * @license OSL 3.0
 * @copyright 2026 ITIS COMMERCE (https://www.itis-commerce.com/)
 */
namespace Blueprint\Module\Psmoduleblueprint\Interface;
interface EntityInterface
{
    public function remove($entity): void;
    public function save($entity): void;
    public function set(string $entityClass): self;
}