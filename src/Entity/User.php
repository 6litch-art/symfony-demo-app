<?php

namespace App\Entity;

use App\Entity\Marketplace\Product\Extra\Wallpaper;
use Base\Database\Annotation\DiscriminatorEntry;
use Base\Notifier\Recipient\Recipient;
use Base\Service\Model\AutocompleteInterface;
use Base\Entity\Layout\Attribute\Hyperlink;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Base\Database\Annotation\OrderColumn;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Base\Database\Annotation\Cache;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="userMetadata")
 * @Cache(usage="NONSTRICT_READ_WRITE", associations="ALL")
 * @DiscriminatorEntry
 */
class User extends \Base\Entity\User implements AutocompleteInterface
{
    public function __toString(): string { return $this->getFirstname() ?? parent::__toString(); }
    public function __autocomplete(): string { return $this->getFullname() ?? $this->__toString(); }
    public function __autocompleteData(): array { return []; }

    public function __construct(?string $email = null, ?string $firstname = null, ?string $lastname = null)
    {
        parent::__construct($email);
        $this->firstname = $firstname;
        $this->lastname  = $lastname;

        $this->setIsVerified(true);
        $this->setIsApproved(true);

        $this->hyperlinks = new ArrayCollection();
        $this->array = [];
        $this->arrayAssociative = [];
    }

    /**
     * @ORM\Column(type="json", length=255)
     */
    protected $array;
    public function getArray()
    {
        return $this->array;
    }
    public function setArray($array)
    {
        $this->array = $array;
        return $this;
    }

    /**
     * @ORM\Column(type="json", length=255)
     */
    protected $arrayAssociative;
    public function getArrayAssociative()
    {
        return $this->arrayAssociative;
    }
    public function setArrayAssociative($arrayAssociative)
    {
        $this->arrayAssociative = $arrayAssociative;
        return $this;
    }

    public function getRecipient(): Recipient
    {
        $email  = $this->getFullname() . " <" . $this->getEmail() . ">";
        $phone  = $this->getPhone();
        $locale = $this->getLocale();

        return new Recipient($email, $phone ?? '', $locale);
    }

    public function getFormattedAvatar(): ?string
    {
        if ($this->getAvatarFile()) {
            $avatar = "<img src='" . $this->getAvatar() . "'>";
        } else {
            $avatar = "<i class='fas fa-grin-stars'></i>";
        }

        return '<span data-toggle="tooltip" data-placement="top" title="' . $this . '">' . $avatar . '</span>';
    }

    public function getUserIdentifier(): string { return $this->getEmail() ?? ""; }
    public function getFullname(): string { return trim($this->firstname . " " . $this->lastname); }

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $firstname;
    public function getFirstname(): ?string { return $this->firstname; }
    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;
        return $this;
    }

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $lastname;
    public function getLastname(): ?string { return $this->lastname; }
    public function setLastname(?string $lastname): self
    {
        $this->lastname = $lastname;
        return $this;
    }

    /**
     * @ORM\ManyToMany(targetEntity=Hyperlink::class, orphanRemoval=true, cascade={"persist", "remove"})
     * @OrderColumn
     */
    protected $hyperlinks;
    public function getHyperlinks(): Collection { return $this->hyperlinks; }
    public function addHyperlink(Hyperlink $hyperlink): self
    {
        if (!$this->hyperlinks->contains($hyperlink)) {
            $this->hyperlinks[] = $hyperlink;
        }

        return $this;
    }

    public function removeHyperlink(Hyperlink $hyperlink): self
    {
        $this->hyperlinks->removeElement($hyperlink);
        return $this;
    }
}
