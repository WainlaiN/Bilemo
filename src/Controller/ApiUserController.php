<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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
     * @Route("api/user/{id}", name="api_user_show", methods={"GET"})
     * @param User $user
     * @return JsonResponse
     */
    public function show(User $user)
    {
        return $this->json($user, 200, [], ['groups' => 'user:read']);
    }

    /**
     * @Route("/api/user", name="api_user_create", methods={"POST"})
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $manager
     * @param ValidatorInterface $validator
     * @return JsonResponse
     */
    public function create(
        Request $request,
        SerializerInterface $serializer,
        EntityManagerInterface $manager,
        ValidatorInterface $validator,
        UrlGeneratorInterface $urlGenerator
    ) {
        $json = $request->getContent();

        try {

            $user = $serializer->deserialize($json, User::class, 'json');

            $errors = $validator->validate($user);

            if (count($errors) > 0) {
                return $this->json($errors, 400);
            }

            $manager->persist($user);
            $manager->flush();

            return $this->json(
                $user,
                201,
                [
                    "Location" => $urlGenerator->generate("api_user_show", ["id" => $user->getId()]),
                ],
                ['groups' => 'user:read']
            );

        } catch (NotEncodableValueException $e) {
            return $this->json(
                [
                    'status' => 400,
                    'message' => $e->getMessage(),
                ],
                400,

            );

        }
    }

    /**
     * @Route("api/user/{id}", name="api_user_put", methods={"PUT"})
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $manager
     * @param ValidatorInterface $validator
     * @return JsonResponse
     */
    public function put(
        User $user,
        Request $request,
        SerializerInterface $serializer,
        EntityManagerInterface $manager,
        ValidatorInterface $validator
    ) {

        $json = $request->getContent();
        $user = $serializer->deserialize(
            $json,
            User::class,
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $user]
        );

        $errors = $validator->validate($user);

        if (count($errors) > 0) {
            return $this->json($errors, 400);
        }

        $manager->persist($user);
        $manager->flush();

        return $this->json(null, 204, []);

    }
}
