<?php

namespace App\Controller;

use App\Repository\ClientRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ApiClientController extends AbstractController
{
    /**
     * @Route("/api/client", name="api_client_index", methods={"GET"})
     * @param ClientRepository $clientRepository
     * @param NormalizerInterface $normalizer
     * @return JsonResponse
     * @throws ExceptionInterface
     */

    public function index(ClientRepository $clientRepository, NormalizerInterface $normalizer)
    {
        $clients = $clientRepository->findAll();

        $normalisesClients = $normalizer->normalize($clients, null, ['groups' => 'client:read']);

        dd( $normalisesClients);

        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/ApiClientController.php',
        ]);
    }
}
