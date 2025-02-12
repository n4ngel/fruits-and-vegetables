<?php

namespace App\Serializer;

use App\Entity\Produce;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class ProduceDenormalizer implements DenormalizerInterface
{
	/**
	 * @inheritDoc
	 */
	public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): Produce
	{
		$produce = new Produce();
		$produce->setName($data['name']);
		$produce->setType($data['type']);
		$produce->setExternalId($data['id']);

		if ($data['unit'] === 'kg') {
			$weight = $data['quantity'] * 1000;
		} else {
			$weight = $data['quantity'];
		}

		$produce->setWeight($weight);

		return $produce;
	}

	/**
	 * @inheritDoc
	 */
	public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
	{
		return $type === Produce::class;
	}

	/**
	 * @inheritDoc
	 */
	public function getSupportedTypes(?string $format): array
	{
		return [
			Produce::class => true
		];
	}
}