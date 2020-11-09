<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Service\CacheContent;
use App\Service\HateoasService;
use App\Service\PaginatorService;
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
     * @var HateoasService
     */
    private $hateoasService;

    /**
     * ApiProductController constructor.
     *
     * @param HateoasService $hateoasService
     */
    public function __construct(HateoasService $hateoasService)
    {
        $this->hateoasService = $hateoasService;
    }


    /**
     * Paginate products list.
     *
     * This call display all products with paginations.
     *
     * @Route("/api/products/{page}", name="product_list", methods={"GET"}, requirements={"page"="\d+"})
     *
     * @OA\Parameter(
     *     name="page",
     *     in="path",
     *     description="resource page",
     *     required=true,
     *     @OA\Schema (type="integer")
     *     ),
     * @OA\Response(
     *     response=200,
     *     description="Returns products list",
     *     @Model(type=Product::class)
     *     )),
     * @OA\Response(
     *     response=404,
     *     description="Page Not found",
     *     @OA\JsonContent(example="Only 5 pages available.")
     * )
     * @param $page
     * @param ProductRepository $productRepository
     * @param PaginatorService $paginator
     * @param Request $request
     * @param CacheContent $cacheContent
     * @return JsonResponse
     */
    public function getProductsByPage(
        $page,
        ProductRepository $productRepository,
        PaginatorService $paginator,
        Request $request,
        CacheContent $cacheContent
    ) {
        $query = $productRepository->findPageByProduct();

        //get page data with page limit
        $data = $paginator->paginate($query, '10', $page);

        $json = $this->hateoasService->serializeHypermedia($data);

        $response = new JsonResponse($json, 200, [], true);

        //return cached response
        return $cacheContent->addToCache($request, $response);
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
     *     description="Returns product detail",
     *     @Model(type=Product::class)
     *     )),
     * @OA\Response(
     *     response=404,
     *     description="Product Not found",
     *     @OA\JsonContent(example="Product not found.")
     *     ))
     *
     *
     * @ParamConverter("product", converter="product_get")
     * @param Product $product
     * @return JsonResponse
     */
    public function show(Product $product)
    {
        $json = $this->hateoasService->serializeHypermedia($product);

        return new JsonResponse($json, 200, [], true);
    }
}
