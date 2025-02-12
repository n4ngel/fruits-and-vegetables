<?php

namespace App\Serializer;

use App\Entity\Produce;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ProduceNormalizer implements NormalizerInterface
{
	/**
	 * @inheritDoc
	 */
	public function normalize(mixed $data, ?string $format = null, array $context = []): array|string|int|float|bool|\ArrayObject|null
	{
		return [
			'id' => $data->getExternalId(),
			'name' => $data->getName(),
			'type' => $data->getType(),
			'quantity' => isset($context['unit']) && $context['unit'] === 'kg' ? $data->getWeight() / 1000 : $data->getWeight(),
			'unit' => $context['unit'] ?? 'g',
		];
	}

	/**
	 * @inheritDoc
	 */
	public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
	{
		return $data instanceof Produce;
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