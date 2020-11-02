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
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints as Assert;
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
     * @OA\Response(
     *     response=201,
     *     description="User added",
     *     @OA\JsonContent(type="array",@OA\Items(ref=@Model(type=Client::class, groups={"client:read"}))
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
     * @return JsonResponse
     */
    public function register (Request $request, UserPasswordEncoderInterface $encoder, EntityManagerInterface $manager, ValidatorInterface $validator)
    {
        $data = json_decode($request->getContent(), true);

        $email = $data['email'];
        $name = $data['name'];
        $password = $data['password'];

        $client = new Client();

        $client->setEmail($email);
        $client->setName($name);
        $client->setPassword($encoder->encodePassword($client, $password));
        $client->setRoles();

        $violations = $validator->validate($client, null, "register");

        if ($violations->count() > 0) {
            return new JsonResponse(["error" => (string)$violations], 400);
        }

        try {
            $manager->persist($client);
            $manager->flush();
        } catch (\Exception $e) {
            return new JsonResponse(["error" => $e->getMessage()], 500);
        }
        return new JsonResponse(["success" => $client->getUsername(). " has been registered!"], 200);
    }
}



