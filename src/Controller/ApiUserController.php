<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security as OASecurity;

/**
 * Class ApiUserController
 *
 * @package App\Controller
 * @OASecurity(name="Bearer")
 * @OA\Tag(name="User")
 */
class ApiUserController extends AbstractController
{
    /**
     * @var Security
     */
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * List users from current client.
     *
     * This call display all users belonging to client.
     *
     * @Route("/api/user", name="api_user_index", methods={"GET"})
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns users list",
     *     @OA\JsonContent(type="array",@OA\Items(ref=@Model(type=User::class, groups={"client:read"}))
     *     )
     * )
     *
     * @param UserRepository $userRepository
     * @return JsonResponse
     */
    public function index(UserRepository $userRepository, Request $request)
    {

        $page = $request->query->get('page', 1);


        $qb = $userRepository->findByClient($this->security->getUser());

        $adapter = new QueryAdapter($qb);

        $pagerfanta = new Pagerfanta($adapter);

        $pagerfanta->setMaxPerPage(5);

        $pagerfanta->setCurrentPage($page);

        $users = [];

        foreach ($pagerfanta->getCurrentPageResults() as $result) {

            $users[] = $result;
        }

        $response = new JsonResponse([
            'total' => $pagerfanta->getNbResults(),
            'count' => count($users),
            'programmers' => $users,
        ], 200);



        //return $this->json($users, 200, [], ['groups' => 'client:read']);

    }

    /**
     * List user detail from current client.
     *
     * This call display user detail from client.
     *
     * @Route("api/user/{id}", name="api_user_show", methods={"GET"})
     *
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="resource ID",
     *     required=true,
     *     @OA\Schema (type="integer")
     *     ),
     * @OA\Response(
     *     response=200,
     *     description="Returns the user detail",
     *     @OA\JsonContent(type="array",@OA\Items(ref=@Model(type=User::class, groups={"client:read"}))
     *     )),
     * @OA\Response(
     *     response=404,
     *     description="Client Not found",
     *     @OA\JsonContent(type="array",@OA\Items(ref=@Model(type=User::class, groups={"client:read"}))
     *     )
     * )
     *
     * @param User $user
     * @return JsonResponse
     *
     */
    public function show(User $user)
    {
        $current_client = $this->getUser();

        if ($current_client === $user->getClient()) {
            return $this->json($user, 200, [], ['groups' => 'client:read']);
        }

        return $this->json(
            [
                'status' => 404,
                'message' => "Client introuvable",
            ],
            400
        );
    }

    /**
     * Add new user from current client.
     *
     * This call add a user for connected client.
     *
     * @Route("/api/user", name="api_user_post", methods={"POST"})
     *
     * @OA\Response(
     *     response=201,
     *     description="User added",
     *     @OA\JsonContent(type="array",@OA\Items(ref=@Model(type=User::class, groups={"client:read"}))
     *     )),
     * @OA\Response(
     *     response=400,
     *     description="Invalid JSON",
     *     @OA\JsonContent(type="array",@OA\Items(ref=@Model(type=User::class, groups={"client:read"}))
     *     )
     * )
     *
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
                ['groups' => 'client:read']
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
     * Delete user from current client.
     *
     * This call delete a user for connected client.
     *
     * @Route("api/user/{id}", name="api_user_delete", methods={"DELETE"})
     *
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="resource ID",
     *     required=true,
     *     @OA\Schema (type="integer")
     *     ),
     * @OA\Response(
     *     response=204,
     *     description="User deleted"
     *     )
     * )
     *
     * @param User $user
     * @param EntityManagerInterface $manager
     * @return JsonResponse
     */
    public function delete(User $user, EntityManagerInterface $manager)
    {

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
