<?php

namespace App\Controller;

use App\Entity\Client;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class ApiClientController
 *
 * @package App\Controller
 * @OA\Tag(name="Client")
 *
 * @package App\Controller
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
     *     @OA\JsonContent(description="Returned when the client is not validated.")
     *     )
     * )
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function register (Request $request, UserPasswordEncoderInterface $encoder, EntityManagerInterface $manager)
    {
        $data = json_decode($request->getContent(), true);

        $validator = Validation::createValidator();

        $constraint = new Assert\Collection(array(
            // the keys correspond to the keys in the input array
            'email' => new Assert\Email(),
            'name' => new Assert\Length(array('min' => 2)),
            'password' => new Assert\Length(array('min' => 8)),

        ));
        $violations = $validator->validate($data, $constraint);


        if ($violations->count() > 0) {
            return new JsonResponse(["error" => (string)$violations], 400);
        }

        $email = $data['email'];
        $name = $data['name'];
        $password = $data['password'];

        $client = new Client();

        $client->setEmail($email);
        $client->setName($name);
        $client->setPassword($encoder->encodePassword($client, $password));
        $client->setRoles();

        try {
            $manager->persist($client);
            $manager->flush();
        } catch (\Exception $e) {
            return new JsonResponse(["error" => $e->getMessage()], 500);
        }
        return new JsonResponse(["success" => $client->getUsername(). " has been registered!"], 200);
    }
}



