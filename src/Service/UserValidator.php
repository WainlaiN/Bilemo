<?php


namespace App\Service;


use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Security\Core\Security;

class UserValidator
{
    /**
     * @var Security
     */
    private $security;

    private $manager;

    private $validator;

    private $urlGenerator;

    public function __construct(
        EntityManagerInterface $manager,
        ValidatorInterface $validator,
        Security $security,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->manager = $manager;
        $this->validator = $validator;
        $this->security = $security;
        $this->urlGenerator = $urlGenerator;

    }

    public function validateUser($user)
    {

        return $this->validator->validate($user);

        /**if (count($errors) > 0) {
            return $errors;
        }

        $this->persistUser($user);**/



    }

    private function persistUser(User $user)
    {
        $current_client = $this->security->getUser();
        $user->setClient($current_client);
        $this->manager->persist($user);
        $this->manager->flush();

        return $user;

    }
}