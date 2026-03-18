<?php
/**
 * @author Omid AMINI
 * @link https://www.linkedin.com/in/omid-amini/
 * @license OSL 3.0
 */
namespace Blueprint\Module\Psmoduleblueprint\Entity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * @ORM\Entity()
 * @ORM\Table()
 */
class Lang
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="id_lang")
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
    /**
     * @ORM\Column(type="string", length=2, unique=true)
     */
    private $isoCode;
    /**
     * @ORM\Column(type="string", length=5, unique=true)
     */
    private $languageCode;
    public function getId(): int
    {
        return $this->id;
    }
    /**
     * Get the name of the language
     *
     * @return string
     */
    public function getName(): string
    {        
        return $this->name;
    }
    /**
     * Set the name of the language
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
    /**
     * Get the ISO code of the language
     *
     * @return string
     */
    public function getIsoCode(): string
    {
        return $this->isoCode;
    }
    /**
     * Set the ISO code of the language
     * @param string $isoCode
     * @return self
     */
    public function setIsoCode(string $isoCode): self
    {
        $this->isoCode = $isoCode;
        return $this;
    }
    /**
     * Get the language code
     * @return string
     */
    public function getLanguageCode(): string
    {
        return $this->languageCode;
    }
    /**
     * Set the language code
     * @param string $languageCode
     * @return self
     */
    public function setLanguageCode(string $languageCode): self
    {
        $this->languageCode = $languageCode;
        return $this;
    }
}
