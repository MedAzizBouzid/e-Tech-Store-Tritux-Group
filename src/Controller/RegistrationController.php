<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\UserAuthenticator;
use App\Service\JwtService;
use App\Service\SendMailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;


class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        UserAuthenticatorInterface $userAuthenticator,
        UserAuthenticator $authenticator,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger,
        sendMailService $mail,
        JwtService $jwt
    ): Response {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
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

            $entityManager->persist($user);
             $entityManager->flush();

            $header =[
                'typ'=>'JWT',
                'alg'=>'HS256'
            ];
            $payload =[
                'user_id'=>$user->getId()
            ];
            $token = $jwt->generate($header,$payload,$this->getParameter('app.jwtsecret'));

            // do anything else you need here, like send an email

            $mail->send(
                'no-reply@monsite.net',
                $user->getEmail(),
                'Activation de votre Compte E-tech store',
                'register',
                compact('user','token')

            );
            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
    #[Route('/verify/{token}', name: 'app_verify_email')]
    public function verifyUser($token,JWTService $jwt,UserRepository $userRepository, EntityManagerInterface $em): Response
    {   
      // dd($jwt->check($token,$this->getParameter('app.jwtsecret')));
      //  dd($jwt->isExpired($token));
       if( $jwt->isValid($token)&& !$jwt->isExpired($token)&& $jwt->check($token,$this->getParameter('app.jwtsecret'))){
        $payload=$jwt->getPayload($token);
        $user=$userRepository->find($payload['user_id']);
        if($user && !$user->getIsVerified()){
            $user->setIsVerified(true);
            $em->flush($user);
            // $this->sendSmsAction();
            $this->addFlash('success', 'Your email address has been verified.');
            return $this->redirectToRoute('app_front');

        }
       }
       $this->addFlash('danger','token invalid');

        return $this->redirectToRoute('app_front');
    }
}
