<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\CacheContent;
use App\Service\HateoasService;
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
    /**
     * @var HateoasService
     */
    private $hateoasService;

    public function __construct(Security $security, HateoasService $hateoasService)
    {
        $this->security = $security;
        $this->hateoasService = $hateoasService;
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
     *     @Model(type=User::class)
     *     )),
     * @OA\Response(
     *     response=404,
     *     description="Page Not found",
     *     @OA\JsonContent(example="Only 5 pages available.")
     *     )
     *
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

        //get page data with page limit
        $data = $paginator->paginate($query, '5', $page);

        $json = $this->hateoasService->serializeHypermedia($data);

        $response = new JsonResponse($json, 200, [], true);

        //return cached response
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
     *     description="Returns user detail",
     *     @Model(type=User::class)
     *     )),
     * @OA\Response(
     *     response=404,
     *     description="User Not found",
     *     @OA\JsonContent(example="User not found.")
     * )
     *
     * @ParamConverter("user", converter="user_get")
     * @param User $user
     * @return JsonResponse
     *
     */
    public function show(User $user)
    {
       $json = $this->hateoasService->serializeHypermedia($user);

        return new JsonResponse($json, 200, [], true);
    }

    /**
     * Add new user from current client.
     *
     * This call add a user for connected client.
     *
     * @Route("/api/user", name="api_user_post", methods={"POST"})
     *
     * @OA\Parameter(
     *     name="username",
     *     in="query",
     *     description="Username",
     *     required=true,
     *     @OA\Schema (type="string")
     *     ),
     * @OA\Parameter(
     *     name="email",
     *     in="query",
     *     description="User email",
     *     required=true,
     *     @OA\Schema (type="string")
     *     ),
     * @OA\Response(
     *     response=201,
     *     description="Returns user added",
     *     @Model(type=User::class)
     *     )),
     * @OA\Response(
     *     response=400,
     *     description="Invalid JSON",
     *     @OA\JsonContent(example="Control character error, possibly incorrectly encoded.")
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
     *     @OA\JsonContent(example="User not found.")
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
                'message' => "User not found",
            ],
        );
    }
}
