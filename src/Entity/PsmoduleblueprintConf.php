<?php
/**
 * @author Omid AMINI
 * @link https://www.linkedin.com/in/omid-amini/
 * @license OSL 3.0
 */
declare(strict_types=1);

namespace Blueprint\Module\Psmoduleblueprint\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="psmoduleblueprint_conf")
 */
class PsmoduleblueprintConf
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=64, unique=true)
     */
    private string $name = '';

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $value = '';

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        if (!preg_match('/^[A-Za-z0-9_]{1,64}$/', $name)) {
            throw new \InvalidArgumentException('Invalid configuration name.');
        }

        $this->name = $name;

        return $this;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        if (strlen($value) > 255 || str_contains($value, "\0")) {
            throw new \InvalidArgumentException('Invalid configuration value.');
        }

        $this->value = $value;

        return $this;
    }
}
