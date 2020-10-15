<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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

        $normalisesusers = $normalizer->normalize($users, null, ['groups' => 'user:read']);

        dd($normalisesusers);
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/ApiUserController.php',
        ]);
    }
}
