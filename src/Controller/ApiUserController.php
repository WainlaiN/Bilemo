<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\CacheContent;
use App\Service\PaginatorService;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Validator\ValidatorInterface;

use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security as OASecurity;

/**
 * Class ApiUserController
 *
 * @package App\Controller
 *
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
     * Paginate users list from current client.
     *
     * This call display all users belonging to client with pagination.
     *
     *
     * @Route("/api/users/{page}", name="user_list", methods={"GET"}, requirements={"page"="\d+"})
     *
     * @OA\Parameter(
     *     name="page",
     *     in="path",
     *     description="resource page",
     *     required=true,
     *     @OA\Schema (type="integer")
     *     ),
     * @OA\Response(
     *     response=200,
     *     description="Returns users list",
     *     @OA\JsonContent(type="array",@OA\Items(ref=@Model(type=User::class, groups={"client:read"}))
     *     )),
     * @OA\Response(
     *     response=404,
     *     description="Page Not found",
     *     @OA\JsonContent(description="Returned when the page is not found.")
     * )
     *
     */
    public function getUsersByPage(
        $page,
        UserRepository $userRepository,
        PaginatorService $paginator,
        Request $request,
        CacheContent $cacheContent
    ) {
        $query = $userRepository->findPageByClient($this->security->getUser());

        $data = $paginator->paginate($query, '5', $page);

        $response = $this->json($data, 200, [], ['groups' => 'client:read']);

        return $cacheContent->addToCache($request, $response);
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
     *     description="User Not found",
     *     @OA\JsonContent(description="Returned when the user is not found.")
     * )
     *
     * @ParamConverter("user", converter="user_get")
     * @param User $user
     * @return JsonResponse
     *
     */
    public function show(User $user)
    {
        return $this->json($user, 200, [], ['groups' => 'client:read']);

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
     *     @OA\JsonContent(description="Returned when the user is not validated.")
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

        if (count($validator->validate($user)) > 0) {
            return $this->json($validator->validate($user), 400);
        }

        $current_client = $this->getUser();
        $user->setClient($current_client);
        $manager->persist($user);
        $manager->flush();

        $url = $urlGenerator->generate("api_user_show", ["id" => $user->getId()]);

        return $this->json(
            $user,
            201,
            ["Location" => $url],
            ['groups' => 'client:read']
        );
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
     *     )),
     * @OA\Response(
     *     response=404,
     *     description="User Not found",
     *     @OA\JsonContent(description="Returned when the user is not found.")
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
                'status' => 404,
                'message' => "Client introuvable",
            ],
        );
    }
}
