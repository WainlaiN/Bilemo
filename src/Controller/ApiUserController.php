<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class ApiUserController extends AbstractController
{

    /**
     * @Route("/api/user", name="api_user_index", methods={"GET"})
     * @param UserRepository $userRepository
     * @return JsonResponse
     */
    public function index(UserRepository $userRepository)
    {
        $users = $userRepository->findAll();

        return $this->json($users, 200, [], ['groups' => 'user:read']);

    }

    /**
     * @Route("/api/user", name="api_user_create", methods={"POST"})
     * @param UserRepository $userRepository
     * @return JsonResponse
     */
    public function create(Request $request, SerializerInterface $serializer, EntityManagerInterface $manager)
    {
        $json = $request->getContent();

        $user = $serializer->deserialize($json, User::class, 'json');

        $manager->persist($user);
        $manager->flush();

        return $this->json($user, 201, [], ['groups' => 'user:read']);


    }
}
