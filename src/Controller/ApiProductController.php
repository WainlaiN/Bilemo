<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Service\CacheContent;
use ContainerVkLJQnR\getLexikJwtAuthentication_CheckConfigCommandService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security as OASecurity;

/**
 * Class ApiProductController
 *
 * @package App\Controller
 * @OASecurity(name="Bearer")
 * @OA\Tag(name="Product")
 */
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
     *     @OA\JsonContent(type="array",@OA\Items(ref=@Model(type=Product::class, groups={"product:read"}))
     *     )
     * )
     *
     * @param ProductRepository $productRepository
     * @return JsonResponse
     */
    public function index(ProductRepository $productRepository, Request $request, CacheContent $cacheContent)
    {
        $products = $productRepository->findAll();

        $response = $this->json($products, 200, [], ['groups' => 'product:read']);

        return $cacheContent->CheckCache($request, $response);

    }





    /**
     * Product detail.
     *
     * This call display a product detail.
     *
     * @Route("api/product/{id}", name="api_product_show", methods={"GET"})
     *
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="resource ID",
     *     required=true,
     *     @OA\Schema (type="integer")
     *     ),
     * @OA\Response(
     *     response=200,
     *     description="Returns the product detail",
     *     @OA\JsonContent(type="array",@OA\Items(ref=@Model(type=Product::class, groups={"product:read"}))
     *     )),
     * @OA\Response(
     *     response=404,
     *     description="Product Not found",
     *     @OA\JsonContent(type="array",@OA\Items(ref=@Model(type=Product::class, groups={"product:read"}))
     *     ))
     *
     *
     * @ParamConverter("product", converter="product_get")
     * @param Product $product
     * @return JsonResponse
     */
    public function show(Product $product)
    {
        return $this->json($product, 200, [], ['groups' => 'product:read']);
    }
}
