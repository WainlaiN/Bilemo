<?php


namespace App\Request\ParamConverter;


use App\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class UserConverter
 * @package App\Request\ParamConverter
 */
class UserConverter implements ParamConverterInterface
{

    /**
     * @var SerializerInterface
     */
    private SerializerInterface $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $serializer = $this->getSerializer();
    }

    public function apply(Request $request, ParamConverter $configuration)
    {
        $user = $this->serializer->deserialize($request->getContent(), User::class, 'json');

        $request->attributes->set($configuration->getName(), $user);


    }

    public function supports(ParamConverter $configuration)
    {
        return $configuration->getName() === "user";
    }
}