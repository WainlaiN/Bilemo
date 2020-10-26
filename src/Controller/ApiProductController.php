<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;


class ApiProductController extends AbstractController
{
    /**
     * List all products.
     *
     * This call display all products.
     *
     * @Route("/api/product", name="api_product_index", methods={"GET"})
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns the products list",
     *     @OA\JsonContent(type="string", description="test"),
     *     @OA\Schema(
     *         type="array",
     *         @OA\Items(ref=@Model(type=Product::class, groups={"product:read"}))
     *     )
     * )
     *
     * @param ProductRepository $productRepository
     * @return JsonResponse
     */
    public function index(ProductRepository $productRepository)
    {
        $products = $productRepository->findAll();

        return $this->json($products, 200, [], ['groups' => 'product:read']);
    }

    /**
     * Product detail.
     *
     * This call display a product detail.
     *
     * @Route("api/product/{id}", name="api_product_show", methods={"GET"})
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns the product detail",
     *     @OA\Schema(
     *         type="array",
     *         @OA\Items(ref=@Model(type=Product::class, groups={"product:read"}))
     *     )
     * )
     *
     *
     * @param Product $product
     * @return JsonResponse
     */
    public function show(Product $product)
    {
        return $this->json($product, 200, [], ['groups' => 'product:read']);
    }
}
