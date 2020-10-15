<?php

namespace App\Controller;

use App\Repository\ClientRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ApiClientController
 *
 * @package App\Controller
 */
class ApiClientController extends AbstractController
{

    /**
     * @Route("/api/client", name="api_client_index", methods={"GET"})
     * @param ClientRepository $clientRepository
     * @return JsonResponse
     */
    public function index(ClientRepository $clientRepository)
    {
        $clients = $clientRepository->findAll();

        return $this->json($clients, 200, [], ['groups' => 'client:read']);
    }
}
