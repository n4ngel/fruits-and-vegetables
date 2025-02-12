<?php

namespace App\Serializer;

use App\Entity\Produce;
use PHPUnit\Framework\TestCase;

class ProduceDenormalizerTest extends TestCase
{
	private ProduceDenormalizer $denormalizer;

	protected function setUp(): void
	{
		$this->denormalizer = new ProduceDenormalizer();
	}

	public function testDenormalizeWithKgUnit(): void
	{
		$data = [
			'name' => 'Apples',
			'type' => 'fruit',
			'id' => 100,
			'unit' => 'kg',
			'quantity' => 2
		];

		$produce = $this->denormalizer->denormalize($data, Produce::class);

		$this->assertInstanceOf(Produce::class, $produce);
		$this->assertSame('Apples', $produce->getName());
		$this->assertSame('fruit', $produce->getType());
		$this->assertSame(100, $produce->getExternalId());
		$this->assertSame(2000, $produce->getWeight());
	}

	public function testDenormalizeWithNonKgUnit(): void
	{
		$data = [
			'name' => 'Carrot',
			'type' => 'vegetable',
			'id' => 200,
			'unit' => 'g',
			'quantity' => 500
		];

		$produce = $this->denormalizer->denormalize($data, Produce::class);

		$this->assertInstanceOf(Produce::class, $produce);
		$this->assertSame('Carrot', $produce->getName());
		$this->assertSame('vegetable', $produce->getType());
		$this->assertSame(200, $produce->getExternalId());
		$this->assertSame(500, $produce->getWeight());
	}

	public function testSupportsDenormalizationReturnsTrueForProduceClass(): void
	{
		$supports = $this->denormalizer->supportsDenormalization([], Produce::class);

		$this->assertTrue($supports);
	}

	public function testSupportsDenormalizationReturnsFalseForOtherClass(): void
	{
		$supports = $this->denormalizer->supportsDenormalization([], \stdClass::class);

		$this->assertFalse($supports);
	}

	public function testGetSupportedTypesReturnsCorrectArray(): void
	{
		$supportedTypes = $this->denormalizer->getSupportedTypes(null);

		$this->assertIsArray($supportedTypes);
		$this->assertArrayHasKey(Produce::class, $supportedTypes);
		$this->assertTrue($supportedTypes[Produce::class]);
	}
}