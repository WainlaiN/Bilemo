<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ApiUserController extends AbstractController
{
    /**
     * @Route("/api/user", name="api_user_index", methods={"GET"})
     */
    public function index(UserRepository $userRepository, NormalizerInterface $normalizer)
    {
        $users = $userRepository->findAll();

        $normalisesUsers = $normalizer->normalize($users, null, ['groups' => 'user:read']);

        $json = json_encode($normalisesUsers);

        $response = new Response($json, 200, [
            "Content-Type" => "application/json"
        ]);

        return $response;



    }
}
