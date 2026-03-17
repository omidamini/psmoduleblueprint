<?php
/**
 * @author Omid AMINI
 * @link https://www.linkedin.com/in/omid-amini/
 * @license OSL 3.0
 * @copyright 2026 ITIS COMMERCE (https://www.itis-commerce.com/)
 */
namespace Blueprint\Module\Psmoduleblueprint\Entity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * @ORM\Entity()
 * @ORM\Table()
 */
class Shop
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="id_shop")
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=64)
     * @var string
     */
    private $name;
    /**
     * @ORM\Column(type="boolean", options={"default": true})
     */
    private $active;
     /**
     * Get the ID
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }
    /**
     * Get the name of the shop
     *
     * @return string
     */
    public function getName(): string
    {        
        return $this->name;
    }
    /**
     * Set the name of the shop
     * @param string $name
     * @return self
     */
    public function setName(string $name): self
    {        
        $this->name = $name;
        return $this;  
    }
    /**
     * Check if the shop is active
     *
     * @return bool
     */
    public function isActive(): bool
    {        
        return $this->active;
    }
    /**
     * Set the active status of the shop
     * @param bool $active
     * @return self
     */
    public function setActive(bool $active): self
    {   
        $this->active = $active;
        return $this;  
    }
}
