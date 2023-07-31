<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\Product;
use App\Form\CartType;
use App\Repository\CartRepository;
use App\Repository\ProductRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/cart')]
class CartController extends AbstractController
{

    /*          Add product for the first time */
    #[Route('/add/{id}', name: 'app_cart_add', methods: ['GET', 'POST'])]
    public function new(Product $product, Request $request, CartRepository $cartRepository, ProductRepository $productRepository): Response
    {
        if ($product->getQuantity() > 0) {

            $prix = $product->getPrice();

            $existingCart = $cartRepository->findOneBy([
                'user' => $this->getUser(),
                'product' => $product,
                'isEnabled'=>false
            ]);
            if ($existingCart && $existingCart->isIsEnabled()==false) {

                $existingCart->setQuantity($existingCart->getQuantity() + 1);
                $product->setQuantity($product->getQuantity() - 1);
                $existingCart->setTotalP($prix * $existingCart->getQuantity());
                $cartRepository->save($existingCart, true);
                $productRepository->save($product, true);
            } else {

                $existingCart = new Cart();
                $existingCart->setUser($this->getUser());
                $existingCart->setProduct($product);
                $existingCart->setQuantity(1);
                $product->setQuantity($product->getQuantity() - 1);
                $existingCart->setCommande(null);
                $existingCart->setStatus(false);
                $existingCart->setTotalP($prix * $existingCart->getQuantity());
                $cartRepository->save($existingCart, true);
                $productRepository->save($product, true);
            }
        } else $this->addFlash('Danger', 'Sorry Product is out of stock !');

        return $this->redirectToRoute('app_shop', [], Response::HTTP_SEE_OTHER);
    }
    /*          Add product for the first time */
    #[Route('/add_cart/{id}', name: 'app_cart_add_cart', methods: ['GET', 'POST'])]
    public function new2(Product $product, Request $request, CartRepository $cartRepository, ProductRepository $productRepository): Response
    {
        if ($product->getQuantity() > 0) {

            $prix = $product->getPrice();

            $existingCart = $cartRepository->findOneBy([
                'user' => $this->getUser(),
                'product' => $product,
                'isEnabled'=>false
            ]);
            if ($existingCart) {
               
                $existingCart->setQuantity($existingCart->getQuantity() + 1);
               
                $product->setQuantity($product->getQuantity() - 1);
                $existingCart->setTotalP($prix * $existingCart->getQuantity());
                $cartRepository->save($existingCart, true);
                $productRepository->save($product, true);
            } else {

                $existingCart = new Cart();
                $existingCart->setUser($this->getUser());
                $existingCart->setProduct($product);
                $existingCart->setQuantity(1);
                $product->setQuantity($product->getQuantity() - 1);
                $existingCart->setCommande(null);
                $existingCart->setStatus(false);
                $existingCart->setTotalP($prix * $existingCart->getQuantity());
                $cartRepository->save($existingCart, true);
                $productRepository->save($product, true);
            }
        } else $this->addFlash('Danger', 'Sorry Product is out of stock !');

        return $this->redirectToRoute('app_cart', [], Response::HTTP_SEE_OTHER);
    }


    /* Update cart status  */
    #[Route('/check/{id}', name: 'app_status_check', methods: ['GET', 'POST'])]
    public function check(Cart $cart, Request $request, CartRepository $cartRepository): Response
    {
        $cart->setStatus(true);
        $cartRepository->save($cart, true);

        return $this->redirectToRoute('app_cart', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/uncheck/{id}', name: 'app_status_uncheck', methods: ['GET', 'POST'])]
    public function uncheck(Cart $cart, Request $request, CartRepository $cartRepository): Response
    {
        $cart->setStatus(false);
        $cartRepository->save($cart, true);

        return $this->redirectToRoute('app_cart', [], Response::HTTP_SEE_OTHER);
    }
    /* end update cart status */


    /* delete all the product from cart*/
    #[Route("/delete/{id}", name: 'app_cart_delete')]
    public function delete($id, ManagerRegistry $doctrine, CartRepository $cartRepository, ProductRepository $pr)
    {
        $c = $doctrine
            ->getRepository(Cart::class)
            ->find($id);
        $em = $doctrine->getManager();
        $product = $pr->find($c->getProduct()->getId());

        $product->setQuantity($product->getQuantity() + $c->getQuantity());
        $pr->save($product, true);
        $em->remove($c);
        $em->flush();


        return $this->redirectToRoute('app_cart');
    }
    /* end delete all the product from <cart*/

    /* decrement quantity from cart*/
    #[Route('/decrement/{id}', name: 'app_cart_decrement', methods: ['GET', 'POST'])]
    public function Decrement(Cart $cart, Request $request, CartRepository $cartRepository, ProductRepository $pr,$id): Response
    {   if($cart->getQuantity()>1){

        $cart->setQuantity($cart->getQuantity() - 1);
        $product = $pr->find($cart->getProduct()->getId());
        $product->setQuantity($product->getQuantity() + 1);
        $cart->setTotalP($product->getPrice()*$cart->getQuantity());
        $cartRepository->save($cart, true);
    }
    else
     return $this->redirectToRoute('app_cart_delete', ['id' => $id]);

        return $this->redirectToRoute('app_cart', [], Response::HTTP_SEE_OTHER);
    }
    /*end decrement qunatity from cart*/
}
