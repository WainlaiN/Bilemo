<?php


namespace App\Request\ParamConverter\Product;

use App\Repository\ProductRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GetConverter implements ParamConverterInterface
{
    /** @var ProductRepository */
    protected $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function apply(Request $request, ParamConverter $configuration)
    {
        $id = $request->get('id');


        if (!$this->productRepository->find($id)) {

            throw new NotFoundHttpException("Product not found");

        }

        $request->attributes->set($configuration->getName(), $this->productRepository->find($id));

    }

    public function supports(ParamConverter $configuration)
    {
        return $configuration->getName() === "product";
    }
}