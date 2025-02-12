<?php

namespace App\Tests\App\Service;

use App\Entity\Produce;
use App\Service\StorageService;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\SerializerInterface;

class StorageServiceTest extends TestCase
{
	public function testReceivingRequest(): void
	{
		$request = file_get_contents('request.json');

		$serializer = $this->createMock(SerializerInterface::class);
		$entityManager = $this->createMock(EntityManagerInterface::class);

		$storageService = new StorageService($request, $serializer, $entityManager);

		$this->assertNotEmpty($storageService->getRequest());
		$this->assertIsString($storageService->getRequest());
		$this->assertIsArray(json_decode($storageService->getRequest(), true));
		$this->assertNotEmpty(json_decode($storageService->getRequest(), true));
	}

	public function testLoadWithValidRequest(): void
	{
		$request = json_encode([
			['type' => 'fruit', 'name' => 'Apples', 'externalId' => 1, 'weight' => 888],
			['type' => 'vegetable', 'name' => 'Carrots', 'externalId' => 2, 'weight' => 999]
		]);

		$serializer = $this->createMock(SerializerInterface::class);
		$entityManager = $this->createMock(EntityManagerInterface::class);

		$serializer->method('deserialize')->willReturn([
			(new Produce())->setType(Produce::TYPE_FRUIT)->setName('Apple')->setExternalId(1)->setWeight(888),
			(new Produce())->setType(Produce::TYPE_VEGETABLE)->setName('Carrot')->setExternalId(2)->setWeight(999),
		]);

		$entityManager->expects($this->exactly(2))->method('persist');
		$entityManager->expects($this->once())->method('flush');

		$storageService = new StorageService($request, $serializer, $entityManager);

		$storageService->load();
	}

	public function testLoadWithEmptyRequest(): void
	{
		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage('Request must be set');

		$serializer = $this->createMock(SerializerInterface::class);
		$entityManager = $this->createMock(EntityManagerInterface::class);

		$storageService = new StorageService('', $serializer, $entityManager);
		$storageService->load();
	}
}
