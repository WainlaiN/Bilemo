<?php


namespace App\Request\ParamConverter\User;


use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class PutConverter implements ParamConverterInterface
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * PutConverter constructor.
     *
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $manager
     */
    public function __construct(SerializerInterface $serializer, EntityManagerInterface $manager)
    {
        $this->serializer = $serializer;
        $this->manager = $manager;
    }

    public function apply(Request $request, ParamConverter $configuration)
    {
        if (!$request->isMethod('PUT')) {
            return;
        }

        $object = $this->manager->getRepository($configuration->getClass())->find($request->attributes->get("id"));

        $json = $request->getContent();
        $object = $this->serializer->deserialize(
            $json,
            User::class,
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $object]
        );

        $request->attributes->set($configuration->getName(), $object);
    }

    public function supports(ParamConverter $configuration)
    {
        return $configuration->getName() === "user";
    }
}