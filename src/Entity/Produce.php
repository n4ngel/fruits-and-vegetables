<?php

namespace App\Entity;

use App\Repository\ProduceRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Ignore;
use Symfony\Component\Serializer\Attribute\SerializedName;

#[ORM\Entity(repositoryClass: ProduceRepository::class)]
class Produce
{
	public const string TYPE_FRUIT = 'fruit';
	public const string TYPE_VEGETABLE = 'vegetable';

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'SEQUENCE')]
    #[ORM\Column]
    private ?int $id = null;

	#[ORM\Column(length: 50)]
	private ?string $name = null;

    #[ORM\Column]
	#[SerializedName('id')]
    private ?int $externalId = null;

    #[ORM\Column(length: 30)]
    private ?string $type = null;

    #[ORM\Column]
    private ?int $weight = null;

    #[ORM\ManyToOne(inversedBy: 'produce')]
    #[ORM\JoinColumn(nullable: false)]
	#[Ignore]
    private ?ProduceBatch $produceBatch = null;

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

    public function getExternalId(): ?int
    {
        return $this->externalId;
    }

    public function setExternalId(int $externalId): static
    {
        $this->externalId = $externalId;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getWeight(): ?int
    {
        return $this->weight;
    }

    public function setWeight(int $weight): static
    {
        $this->weight = $weight;

        return $this;
    }

    public function getProduceBatch(): ?ProduceBatch
    {
        return $this->produceBatch;
    }

    public function setProduceBatch(?ProduceBatch $produceBatch): static
    {
        $this->produceBatch = $produceBatch;

        return $this;
    }
}
