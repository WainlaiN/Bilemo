<?php


namespace App\Request\ParamConverter\User;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class PostConverter
 * @package App\Request\ParamConverter
 */
class PostConverter implements ParamConverterInterface
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public function apply(Request $request, ParamConverter $configuration)
    {

        if (!$request->isMethod(Request::METHOD_POST)) {
            return;
        }
        $user = $this->serializer->deserialize($request->getContent(), $configuration->getClass(), 'json');

        $request->attributes->set($configuration->getName(), $user);

    }

    public function supports(ParamConverter $configuration)
    {
        return $configuration->getName() === "user";
    }
}