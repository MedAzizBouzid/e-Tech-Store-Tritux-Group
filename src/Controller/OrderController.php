<?php

namespace App\Controller;

use App\Entity\Order;
use App\Form\OrderType;
use App\Repository\CartRepository;
use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/order')]
class OrderController extends AbstractController
{
    #[Route('/', name: 'app_order_index', methods: ['GET'])]
    public function index(OrderRepository $orderRepository): Response
    {
        return $this->render('back/order/index.html.twig', [
            'orders' => $orderRepository->findAll(),
        ]);
    }

    #[Route('/add', name: 'app_order_add', methods: ['GET', 'POST'])]
    public function new(Request $request, OrderRepository $orderRepository,CartRepository $cartRepository): Response
    {   $commande =null;
        $user = $this->getUser();
     

        $carts = $cartRepository->createQueryBuilder('c')
            ->where('c.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();

       
                $order=new Order();
         
                $order->setInsertedAt(new \DateTimeImmutable());
                $order->setStatus(false);
                $order->setTotalO(0);
                 foreach($carts as $cart){
                     if($cart->isStatus()==true && $cart->getCommande()==null)
                     {
                         $cart->setIsenabled(true);   
                         $order->addCart($cart);
                         $order->setTotalO($order->getTotalO()+$cart->getTotalP());
                     }
                   
                 }
                    // 20 is for the shipping fee
                    $order->setTotalO($order->getTotalO()+20);
               
                 $orderRepository->save($order,true);
    
      

       
        $this->addFlash('success','Your order is already in process !');
       
       return $this->redirectToRoute('app_front'); ;
    }
    

    #[Route('/{id}', name: 'app_order_show', methods: ['GET'])]
    public function show(Order $order): Response
    {
        return $this->render('back/order/show.html.twig', [
            'order' => $order,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_order_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Order $order, OrderRepository $orderRepository): Response
    {
        if($order->isStatus()==false)
            $order->setStatus(true);
            else $order->setStatus(false);
            $orderRepository->save($order,true);

      return $this->redirectToRoute('app_order_index');
          
        
    }

    #[Route('/{id}', name: 'app_order_delete', methods: ['POST'])]
    public function delete(Request $request, Order $order, OrderRepository $orderRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$order->getId(), $request->request->get('_token'))) {
            $orderRepository->remove($order, true);
        }

        return $this->redirectToRoute('app_order_index', [], Response::HTTP_SEE_OTHER);
    }
}
