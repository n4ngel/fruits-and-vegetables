<?php

namespace App\Controller;

use App\Service\StorageService;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class InitController extends AbstractController
{
	#[Route('/init', name: 'app_init_index', methods: ['GET', 'POST'])]
	public function index(Request $request, StorageService $storageService): JsonResponse
	{
		if ($request->getMethod() === 'GET') {
			$requestContent = file_get_contents($this->getParameter('kernel.project_dir') . '/request.json');
		} else {
			$requestContent = $request->getContent();
		}

		$storageService->setRequest($requestContent);

		try {
			$storageService->load();
		} catch (UniqueConstraintViolationException $e) {
			return $this->json(['message' => 'Storage already initialized.'], Response::HTTP_CONFLICT);
		} catch (\InvalidArgumentException $e) {
			return $this->json(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
		}

		return $this->json(['message' => 'Storage initialized successfully.'], Response::HTTP_OK);
	}
}
