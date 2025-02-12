<?php

namespace App\Service;

use App\Entity\Produce;
use App\Entity\ProduceBatch;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\String\Slugger\AsciiSlugger;

class StorageService
{
    protected string $request = '';
	private SerializerInterface $serializer;
	private EntityManagerInterface $entityManager;

	public function __construct(
        string $request,
		SerializerInterface $serializer,
		EntityManagerInterface $entityManager,
    )
    {
        $this->request = $request;
		$this->serializer = $serializer;
		$this->entityManager = $entityManager;
    }

    public function getRequest(): string
    {
        return $this->request;
    }

	public function setRequest(string $request): void
	{
		$this->request = $request;
	}

	public function load(): void
	{
		if (empty($this->getRequest())) {
			throw new \InvalidArgumentException('Request must be set');
		}

		$serializedProduce = $this->serializer->deserialize($this->getRequest(), Produce::class.'[]', 'json');

		//add allowed types
		$fruits = $this->createProduceBatch('Fruits');
		$vegetables = $this->createProduceBatch('Vegetables');

		foreach ($serializedProduce as $produce) {
			switch ($produce->getType()) {
				case Produce::TYPE_FRUIT:
					$fruits->addProduce($produce);
					break;
				case Produce::TYPE_VEGETABLE:
					$vegetables->addProduce($produce);
					break;
			}
		}

		$this->entityManager->persist($fruits);
		$this->entityManager->persist($vegetables);

		$this->entityManager->flush();
	}

	//@todo move this logic to separate concerns + could be doctrine sluggable
	private function createProduceBatch(string $name): ProduceBatch
	{
		$batch = (new ProduceBatch())->setName($name);
		$batch->setSlug((new AsciiSlugger())->slug($batch->getName())->lower());

		return $batch;
	}

}
