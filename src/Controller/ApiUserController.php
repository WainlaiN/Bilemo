<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class ApiUserController extends AbstractController
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * @Route("/api/user", name="api_user_index", methods={"GET"})
     * @param UserRepository $userRepository
     * @return JsonResponse
     */
    public function index(UserRepository $userRepository)
    {
        $users = $userRepository->findByClient($this->security->getUser());

        return $this->json($users, 200, [], ['groups' => 'user:read']);

    }

    /**
     * @Route("api/user/{id}", name="api_client_show", methods={"GET"})
     * @param User $user
     * @return JsonResponse
     *
     */
    public function show(User $user)
    {
        $current_client = $this->getUser();
        //dd($current_user);

        if ($current_client === $user->getClient()) {
            return $this->json($user, 200, [], ['groups' => 'user:read']);
        }

        return $this->json(
            [
                'status' => 400,
                'message' => "Client introuvable",
            ],
            400
        );
    }

    /**
     * @Route("/api/user", name="api_user_post", methods={"POST"})
     * @ParamConverter("user", converter="user_post")
     * @param User $user
     * @param EntityManagerInterface $manager
     * @param ValidatorInterface $validator
     * @param UrlGeneratorInterface $urlGenerator
     * @return JsonResponse
     */
    public
    function post(
        User $user,
        EntityManagerInterface $manager,
        ValidatorInterface $validator,
        UrlGeneratorInterface $urlGenerator
    ) {

        try {
            $errors = $validator->validate($user);

            if (count($errors) > 0) {
                return $this->json($errors, 400);
            }

            $current_client = $this->getUser();
            $user->setClient($current_client);
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
                400
            );
        }
    }


    /**
     * @Route("api/user/{id}", name="api_user_put", methods={"PUT"})
     * @ParamConverter("user", converter="user_put")
     * @param User $user
     * @param EntityManagerInterface $manager
     * @param ValidatorInterface $validator
     * @return JsonResponse
     */
    public
    function put(
        User $user,
        EntityManagerInterface $manager,
        ValidatorInterface $validator
    ) {

        $errors = $validator->validate($user);

        if (count($errors) > 0) {
            return $this->json($errors, 400);
        }

        $manager->persist($user);
        $manager->flush();

        return $this->json(null, 204, []);
    }

    /**
     * @Route("api/user/{id}", name="api_user_delete", methods={"DELETE"})
     * @param User $user
     * @param EntityManagerInterface $manager
     * @return JsonResponse
     */
    public
    function delete(
        User $user,
        EntityManagerInterface $manager
    ) {

        $current_client = $this->getUser();
        if ($current_client === $user->getClient()) {

            $manager->remove($user);
            $manager->flush();

            return $this->json(null, 204, []);
        }

        return $this->json(
            [
                'status' => 400,
                'message' => "Client introuvable",
            ],
            400
        );


    }
}
