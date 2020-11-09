<?php

namespace App\Controller;

use App\Entity\Client;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class ApiClientController
 *
 * @package App\Controller
 *
 * @OA\Tag(name="Client")
 */
class ApiClientController extends AbstractController
{

    /**
     * Add new Client.
     *
     * This call add a Client.
     *
     *
     * @Route("/api/client", name="api_client_registration", methods={"POST"})
     *
     * @OA\Parameter(
     *     name="email",
     *     in="query",
     *     description="Client email",
     *     required=true,
     *     @OA\Schema (type="string")
     *     ),
     * @OA\Parameter(
     *     name="name",
     *     in="query",
     *     description="Client name",
     *     required=true,
     *     @OA\Schema (type="string")
     *     ),
     * @OA\Parameter(
     *     name="password",
     *     in="query",
     *     description="Client password",
     *     required=true,
     *     @OA\Schema (type="string")
     *     ),
     *
     * @OA\Response(
     *     response=201,
     *     description="Returns client added",
     *     @Model(type=Client::class)
     *     )),
     * @OA\Response(
     *     response=400,
     *     description="Invalid JSON",
     *     @OA\JsonContent(description="Returned when error in JSON.")
     *     )),
     * @OA\Response(
     *     response=500,
     *     description="Request Problem",
     *     @OA\JsonContent(description="Returned when error while persisting the client.")
     *     ))
     *
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @param EntityManagerInterface $manager
     * @param ValidatorInterface $validator
     * @return JsonResponse
     */
    public function register(
        Request $request,
        UserPasswordEncoderInterface $encoder,
        EntityManagerInterface $manager,
        ValidatorInterface $validator
    ) {
        $data = json_decode($request->getContent(), true);

        $client = new Client();

        $client->setEmail($data['email']);
        $client->setName($data['name']);
        $client->setPassword($data['password']);
        $client->setRoles();

        $violations = $validator->validate($client, null, "register");

        $client->setPassword($encoder->encodePassword($client, $data['password']));

        if ($violations->count() > 0) {
            return $this->json($violations, 400);

        }

        try {
            $manager->persist($client);
            $manager->flush();
        } catch (\Exception $e) {
            return new JsonResponse(["error" => $e->getMessage()], 500);
        }

        return new JsonResponse(["success" => $client->getUsername()." has been registered!"], 201);
    }
}



