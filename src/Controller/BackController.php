<?php

namespace App\Controller;

use App\Repository\CartRepository;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BackController extends AbstractController
{
    #[Route('/back', name: 'app_back')]
    public function index(UserRepository $ur, OrderRepository $or, CartRepository $cr, ProductRepository $pr): Response
    {
        $orders = $or->findBy(['status' => false]);
        $carts = $cr->findBy(['status' => true]);


        //count earnings from orders
        $commandes = $or->findAll();
        $Earnings = 0;

        foreach ($commandes as $commande) {
            $Earnings += $commande->getTotalO();
        }

        /**************************MOST ORDERED PRODUCT************************************************* */

        // Count the occurrences of each product and calculate total quantity
        $productCounts = [];
        $productQuantities = [];
        foreach ($carts as $cart) {
            $product = $cart->getProduct();
            if ($product) {
                $productId = $product->getId();
                if (!isset($productCounts[$productId])) {
                    $productCounts[$productId] = 0;
                    $productQuantities[$productId] = 0;
                }
                $productCounts[$productId]++;
                $productQuantities[$productId] += $cart->getQuantity();
            }
        }

        // Sort products based on both count and total quantity in descending order
        arsort($productCounts);
        arsort($productQuantities);

        // Get the most ordered product ID based on both count and quantity
        $mostOrderedProductId = null;
        $maxOrderCount = null;
        foreach ($productCounts as $productId => $count) {
            if ($maxOrderCount === null) {
                $maxOrderCount = $count;
                $mostOrderedProductId = $productId;
            } elseif ($count === $maxOrderCount) {
                // If two products have the same count, compare their quantities
                if ($productQuantities[$productId] > $productQuantities[$mostOrderedProductId]) {
                    $maxOrderCount = $count;
                    $mostOrderedProductId = $productId;
                }
            } else {
                // Break if we find a product with a lower count
                break;
            }
        }

        // Find the product with the most ordered ID
        $mostOrderedProduct = $pr->find($mostOrderedProductId);

        /**************************************Statistique Prédictive**************************************/
        $historicalEarningsData = [];

        foreach ($commandes as $commande) {
            $date = $commande->getInsertedAt()->format('Y-m-d'); // Format the date as 'Y-m-d'
            $earnings = $commande->getTotalO();

            // Add the date and earnings as a new entry to $historicalEarningsData
            $historicalEarningsData[] = [$date, $earnings];
        }


        // Encode the historical earnings data as JSON
        $jsonHistoricalEarningsData = json_encode($historicalEarningsData);
  
        // Specify the full path to the Python script


        // Call the Python script and pass the historical earnings data as a command-line argument
        // $command = "python3 " . $pythonScriptPath . " " . escapeshellarg($jsonHistoricalEarningsData);
        $command = "python C:\Users\Admin\Desktop\e-Tech-Store\src\Controller\forecast_earnings.py" . " " . escapeshellarg($jsonHistoricalEarningsData);
        dd($command);
        // Execute the Python script using Py4Php
        $forecastedEarnings = shell_exec($command);
        // Debugging: Check the output
  
        // Convert the JSON response from Python to a PHP array
        $forecastedEarnings = json_decode($forecastedEarnings, true);

    


        return $this->render('back/index.html.twig', [
            'orders' => count($orders),
            'mostOrderedProduct' => $mostOrderedProduct,
            'earnings' => $Earnings,
            'usersCount' => count($ur->findAll()),
            'forecasedEarnings' => $forecastedEarnings,

        ]);
    }
   
}
