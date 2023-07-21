<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontController extends AbstractController
{
    #[Route('/', name: 'app_front')]
    public function index(ProductRepository  $categoryRepository): Response
    {      
        return $this->render('front/index.html.twig', [
            'product' => $categoryRepository->findAll(),
        ]);
    }
    #[Route('/shop', name: 'app_shop')]
    public function shop(ProductRepository $pr): Response
    {   

        return $this->render('front/product/productShop.html.twig', [
            'products' => $pr->findAll(),
        ]);
    }
    #[Route('/shop/{id}', name: 'app_product_shop_detail')]
    public function shopDetail(Product $product,ProductRepository $pr): Response
    {   

        return $this->render('front/product/detailProduct.html.twig', [
            'product' => $product,
            'products'=>$pr->findAll()
        ]);
    }
}
