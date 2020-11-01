<?php


namespace App\Service;


use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserValidator
{
    private $jsonResponse;

    private $manager;

    private $validator;

    public function __construct(
        JsonResponse $response,
        EntityManagerInterface $manager,
        ValidatorInterface $validator
    ) {
        $this->jsonResponse = $response;
        $this->manager = $manager;
        $this->validator = $validator;
    }

    public function validateUser($user)
    {
        $response = new JsonResponse();

        try {
            $errors = $this->validator->validate($user);

            if (count($errors) > 0) {
                //return $json($errors, 400);
                $response->setData(array(
                    'error' =>true,
                    'status' => '400'
                ));

                return $response;

            }

            $current_client = $this->getUser();
            $user->setClient($current_client);
            $this->manager->persist($user);
            $this->manager->flush();

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