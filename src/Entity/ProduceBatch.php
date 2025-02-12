<?php

namespace App\Entity;

use App\Repository\ProduceBatchRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProduceBatchRepository::class)]
class ProduceBatch
{
    #[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var Collection<int, Produce>
     */
    #[ORM\OneToMany(targetEntity: Produce::class, mappedBy: 'produceBatch', cascade: ['persist'], fetch: 'LAZY')]
    private Collection $produce;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $slug = null;

    public function __construct()
    {
        $this->produce = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Produce>
     */
    public function getProduce(): Collection
    {
        return $this->produce;
    }

    public function addProduce(Produce $produce): static
    {
        if (!$this->produce->contains($produce)) {
            $this->produce->add($produce);
            $produce->setProduceBatch($this);
        }

        return $this;
    }

    public function removeProduce(Produce $produce): static
    {
        if ($this->produce->removeElement($produce)) {
            // set the owning side to null (unless already changed)
            if ($produce->getProduceBatch() === $this) {
                $produce->setProduceBatch(null);
            }
        }

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }
}
