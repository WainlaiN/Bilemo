<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;


class ApiProductController extends AbstractController
{
    /**
     * @Route("/api/product", name="api_product_index", methods={"GET"})
     * @param ProductRepository $productRepository
     * @return JsonResponse
     */
    public function index(ProductRepository $productRepository)
    {
        $products = $productRepository->findAll();

        return $this->json($products, 200, [], ['groups' => 'product:read']);
    }

    /**
     * @Route("api/product/{id}", name="api_product_show", methods={"GET"})
     * @param Product $product
     * @return JsonResponse
     */
    public function show(Product $product)
    {
        return $this->json($product, 200, [], ['groups' => 'product:read']);
    }
}
