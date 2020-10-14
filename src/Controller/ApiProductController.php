<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;


class ApiProductController extends AbstractController
{
    /**
     * @Route("/api/product", name="api_product_index", methods={"GET"})
     * @param ProductRepository $productRepository
     * @param NormalizerInterface $normalizer
     * @return JsonResponse
     */
    public function index(ProductRepository $productRepository, NormalizerInterface $normalizer)
    {
        $products = $productRepository->findAll();

        $productNormalises = $normalizer->normalize($products);

        dd($productNormalises);


        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/ApiProductController.php',
        ]);
    }
}
