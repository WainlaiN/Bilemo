<?php


namespace App\Service;


use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Validator\Constraints\Json;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserValidator
{
    private $jsonResponse;

    public function __construct(Json $response)
    {
        $this->jsonResponse = $response;
    }

    public function validateUser(
        $user,
        EntityManagerInterface $manager,
        ValidatorInterface $validator
    ) {


        try {
            $errors = $validator->validate($user);

            if (count($errors) > 0) {
                //return $json($errors, 400);
                return $this->jsonResponse($errors, 400);

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

        } catch
        (NotEncodableValueException $e) {
            return $this->json(
                [
                    'status' => 400,
                    'message' => $e->getMessage(),
                ],
                400
            );
        }

    }

}