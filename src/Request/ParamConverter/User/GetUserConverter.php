<?php


namespace App\Request\ParamConverter\User;

use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Security;

class GetUserConverter implements ParamConverterInterface
{
    /** @var UserRepository */
    private $userRepository;

    /**
     * @var Security
     */
    private $security;

    public function __construct(UserRepository $userRepository, Security $security)
    {
        $this->userRepository = $userRepository;
        $this->security = $security;
    }

    public function apply(Request $request, ParamConverter $configuration)
    {
        $current_client = $this->security->getUser();

        $id = $request->get('id');

        $user = $this->userRepository->find($id);

        if ($user == null || $current_client !== $user->getClient()) {

            throw new NotFoundHttpException("User not found");

        } else {

            $request->attributes->set($configuration->getName(), $this->userRepository->find($id));
        }

    }

    public function supports(ParamConverter $configuration)
    {
        return $configuration->getName() === "user";
    }


}