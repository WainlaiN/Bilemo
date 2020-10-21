<?php

namespace App\Controller;

use App\Entity\Client;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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

    /**
     * @Route("api/client/{id}", name="api_user_show", methods={"GET"})
     * @param Client $client
     * @return JsonResponse
     *
     */
    public function show(Client $client)
    {
        return $this->json($client, 200, [], ['groups' => 'client:read']);
    }

}
