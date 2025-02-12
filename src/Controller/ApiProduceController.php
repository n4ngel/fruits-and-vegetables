<?php

namespace App\Controller;

use App\Entity\Produce;
use App\Entity\ProduceBatch;
use App\Repository\ProduceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class ApiProduceController extends AbstractController
{
	#[Route('/api/produce/{slug}', name: 'app_api_produce', methods: ['GET'])]
	public function show(Request $request, ProduceBatch $produceBatch, SerializerInterface $serializer, ValidatorInterface $validator): JsonResponse
	{
		$filters = $request->query->all();
		/**
		 * Example filter: 'weight_gt' => 100  //Matches records where weight is greater than 100
		 */
		$criteria = ProduceRepository::buildCriteriaFromFilters($filters);
		$filteredProduce = $produceBatch->getProduce()->matching($criteria);

		$context = [];
		//@todo refactor as a method with constants
		if (!empty($filters['unit'])) {
			$unit = $filters['unit'];
			//@todo introduce constants
			$unitConstraint = new Assert\Choice(['choices' => ['g', 'kg']]);
			$errors = $validator->validate($unit, $unitConstraint);
			if (count($errors) > 0) {
				return $this->json($errors, 422);
			} else {
				$context['unit'] = $unit;
			}
		}

		return JsonResponse::fromJsonString($serializer->serialize($filteredProduce->toArray(), 'json', $context));
	}

	// @todo validate Produce incoming data vs Produce object validators
	#[Route('/api/produce/{slug}', name: 'app_api_produce_add', methods: ['POST'])]
	public function add(
		ProduceBatch                                     $produceBatch,
		SerializerInterface                              $serializer,
		EntityManagerInterface                           $entityManager,
		#[MapRequestPayload(type: Produce::class)] array $produce): JsonResponse
	{
		/**
		 * Same format as initial format from the request.json is expected as payload
		 */
		foreach ($produce as $item) {
			$produceBatch->addProduce($item);
		}

		//@todo move to repo
		$entityManager->persist($produceBatch);
		$entityManager->flush();

		return new JsonResponse($serializer->serialize($produceBatch->getProduce(), 'json'), Response::HTTP_CREATED, [], true);
	}
}
