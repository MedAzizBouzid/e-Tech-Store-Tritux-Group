<?php

namespace App\Controller;

use App\Entity\Shop;
use App\Form\ShopType;
use App\Repository\ShopRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/boutique')]
class ShopController extends AbstractController
{
    #[Route('/', name: 'app_shop_index', methods: ['GET'])]
    public function index(ShopRepository $shopRepository): Response
    {
        return $this->render('back/shop/index.html.twig', [
            'shops' => $shopRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_shop_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ShopRepository $shopRepository, SluggerInterface $slugger): Response
    {
        $shop = new Shop();
        $form = $this->createForm(ShopType::class, $shop);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $brochureFile = $form->get('image')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $brochureFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $brochureFile->move(
                        $this->getParameter('shop_images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $shop->setImage($newFilename);
            }

            $shopRepository->save($shop, true);

            return $this->redirectToRoute('app_shop_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back/shop/new.html.twig', [
            'shop' => $shop,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_shop_show', methods: ['GET'])]
    public function show(Shop $shop): Response
    {
        return $this->render('back/shop/show.html.twig', [
            'shop' => $shop,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_shop_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Shop $shop, ShopRepository $shopRepository,SluggerInterface $slugger): Response
    {
        $form = $this->createForm(ShopType::class, $shop);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $brochureFile = $form->get('image')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $brochureFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $brochureFile->move(
                        $this->getParameter('shop_images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $shop->setImage($newFilename);
            }
            $shopRepository->save($shop, true);

            return $this->redirectToRoute('app_shop_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back/shop/edit.html.twig', [
            'shop' => $shop,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_shop_delete', methods: ['POST'])]
    public function delete(Request $request, Shop $shop, ShopRepository $shopRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $shop->getId(), $request->request->get('_token'))) {
            $shopRepository->remove($shop, true);
        }

        return $this->redirectToRoute('app_shop_index', [], Response::HTTP_SEE_OTHER);
    }
}
