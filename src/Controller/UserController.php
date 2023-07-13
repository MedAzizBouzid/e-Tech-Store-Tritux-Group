<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Form\UpdatePasswordType;
use App\Form\UserEditType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{
    //----------------Show Users BackOffice
    #[Route('/showAllUsers', name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('back/user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    // #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    // public function new(Request $request, UserRepository $userRepository): Response
    // {
    //     $user = new User();
    //     $form = $this->createForm(User1Type::class, $user);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $userRepository->save($user, true);

    //         return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    //     }

    //     return $this->renderForm('user/new.html.twig', [
    //         'user' => $user,
    //         'form' => $form,
    //     ]);
    // }

    // Detail Profil Backoffice
    #[Route('user/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('back/user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('user/editProfile/{id}', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, UserRepository $userRepository, SluggerInterface $slugger, UserPasswordHasherInterface $hasher): Response
    {

        $form = $this->createForm(UserEditType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($hasher->isPasswordValid($user, $form->get('plainPassword')->getData())) {

                // $userReposgititory->save($user, true);
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
                            $this->getParameter('user_images_directory'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                        // ... handle exception if something happens during file upload
                    }

                    // updates the 'brochureFilename' property to store the PDF file name
                    // instead of its contents
                    $user->setImage($newFilename);
                }
                $user = $form->getData();
                $userRepository->save($user, true);
                return $this->redirectToRoute('app_front', [], Response::HTTP_SEE_OTHER);
            } else {
                $this->addFlash('warning', 'Your password is incorrect');
            }
        }

        return $this->renderForm('back/user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }
    #[Route('user/editPassword/{id}', name: 'app_user_edit_password', methods: ['GET', 'POST'])]
    public function editPassword(Request $request, User $user, UserRepository $userRepository, SluggerInterface $slugger, UserPasswordHasherInterface $hasher): Response
    {
        $form = $this->createForm(UpdatePasswordType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($hasher->isPasswordValid($user, $form->get('plainPassword')->getData())) {
                //$user->setPassword($form->get('newPassword')->getData());
                $user->setPassword(
                    $hasher->hashPassword(
                        $user,
                        $form->get('newPassword')->getData()
                    )
                );
                $userRepository->save($user, true);
                return $this->redirectToRoute('app_front');
            } else
                $this->addFlash('warning', 'Your password is incorrect');
        }


        return $this->render('front/user/editPassword.html.twig', [
            'form' => $form->createView()

        ]);
    }
    //---------Delete Profile BackOffice
    #[Route('user/delete/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, UserRepository $userRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $userRepository->remove($user, true);
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }












    //*****************************************FRONT**********************************************/

    //------------Detail Profil FrontOffice
    #[Route('/profile', name: 'app_profile')]
    public function profile(): Response
    {
        return $this->render('front/user/profileUser.html.twig');
    }
}
