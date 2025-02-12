<?php

namespace App\Serializer;

use App\Entity\Produce;
use PHPUnit\Framework\TestCase;

class ProduceNormalizerTest extends TestCase
{
	private ProduceNormalizer $normalizer;

	protected function setUp(): void
	{
		$this->normalizer = new ProduceNormalizer();
	}

	public function testNormalizeWithDefaultUnit(): void
	{
		$produce = $this->createMock(Produce::class);
		$produce->method('getExternalId')->willReturn(1);
		$produce->method('getName')->willReturn('Apple');
		$produce->method('getType')->willReturn('fruit');
		$produce->method('getWeight')->willReturn(500);

		$result = $this->normalizer->normalize($produce);

		$this->assertEquals([
			'id' => 1,
			'name' => 'Apple',
			'type' => 'fruit',
			'quantity' => 500,
			'unit' => 'g',
		], $result);
	}

	public function testNormalizeWithUnitKilograms(): void
	{
		$produce = $this->createMock(Produce::class);
		$produce->method('getExternalId')->willReturn(1);
		$produce->method('getName')->willReturn('Apple');
		$produce->method('getType')->willReturn('fruit');
		$produce->method('getWeight')->willReturn(1000);

		$context = ['unit' => 'kg'];
		$result = $this->normalizer->normalize($produce, null, $context);

		$this->assertEquals([
			'id' => 1,
			'name' => 'Apple',
			'type' => 'fruit',
			'quantity' => 1.0,
			'unit' => 'kg',
		], $result);
	}

	public function testSupportsNormalizationForValidProduceInstance(): void
	{
		$produce = $this->createMock(Produce::class);

		$this->assertTrue($this->normalizer->supportsNormalization($produce));
	}

	public function testSupportsNormalizationForInvalidInstance(): void
	{
		$this->assertFalse($this->normalizer->supportsNormalization(new \stdClass()));
	}

	public function testGetSupportedTypes(): void
	{
		$this->assertEquals([
			Produce::class => true,
		], $this->normalizer->getSupportedTypes(null));
	}
}