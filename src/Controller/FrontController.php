<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\CartRepository;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use App\Repository\ShopRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontController extends AbstractController
{
    #[Route('/', name: 'app_front')]
    public function index(CartRepository  $cr,ProductRepository $categoryRepository,ShopRepository $shopRepository): Response
    {       $carts=$cr->findByUser($this->getUSer());
        $length=0;
        foreach($carts as $cart)
        {
            if($cart->getCommande()==null)
            $length++;

        }
        return $this->render('front/index.html.twig', [
            'product' => $categoryRepository->findAll(),
            'shops'=>$shopRepository->findAll(),
            'length'=>  $length,
        ]);
    }
    #[Route('/about', name: 'app_about')]
    public function boutique(ShopRepository $sr,CartRepository $cr): Response
    {       $carts=$cr->findByUser($this->getUSer());
        $length=0;
        foreach($carts as $cart)
        {
            if($cart->getCommande()==null)
            $length++;

        }
        return $this->render('front/boutique/aboutUs.html.twig', [
           'shops'=>$sr->findAll(),
           'length'=>  $length,
        ]);
    }
    #[Route('/shop', name: 'app_shop')]
    public function shop(ProductRepository $pr,CartRepository $cr): Response
    {    $carts=$cr->findByUser($this->getUSer());
        $length=0;
        foreach($carts as $cart)
        {
            if($cart->getCommande()==null)
            $length++;

        }

        return $this->render('front/product/productShop.html.twig', [
            'products' => $pr->findAll(),
            'length'=>  $length,
        ]);
    }
    #[Route('/cart', name: 'app_cart')]
    public function cart(ProductRepository $pr,CartRepository $cr): Response
    {   $carts=$cr->findByUser($this->getUSer());
        $length=0;
        foreach($carts as $cart)
        {
            if($cart->getCommande()==null)
            $length++;

        }
       $subtotal=0;
        foreach($carts as $cart){
            if($cart->isStatus() && $cart->getCommande()==null)
            $subtotal=$subtotal+$cart->getTotalP();
        }

        return $this->render('front/cart/cart.html.twig', [
            'carts'=> $carts,
            'length'=> $length,
            'subtotal'=>$subtotal
            
        ]);
    }
    #[Route('/shop/{id}', name: 'app_product_shop_detail')]
    public function shopDetail(Product $product,ProductRepository $pr,CartRepository $cr): Response
    {   
        $carts=$cr->findByUser($this->getUSer());
        $length=0;
        foreach($carts as $cart)
        {
            if($cart->getCommande()==null)
            $length++;

        }
        return $this->render('front/product/detailProduct.html.twig', [
            'product' => $product,
            'products'=>$pr->findAll(),
            'length'=>  $length,
        ]);
    }
    #[Route('/403', name: 'app_403')]
    public function error(ShopRepository $sr,CartRepository $cr): Response
    {       $carts=$cr->findByUser($this->getUSer());
        $length=0;
        foreach($carts as $cart)
        {
            if($cart->getCommande()==null)
            $length++;

        }
        return $this->render('front/403.html.twig', [
           'shops'=>$sr->findAll(),
           'length'=>  $length,
        ]);
    }
    #[Route('/404', name: 'app_404')]
    public function error404(ShopRepository $sr,CartRepository $cr): Response
    {   $carts=$cr->findByUser($this->getUSer());
        $length=0;
        foreach($carts as $cart)
        {
            if($cart->getCommande()==null)
            $length++;

        }
        return $this->render('front/404.html.twig', [
           'shops'=>$sr->findAll(),
           'length'=>  $length,
        ]);
    }
   
}
