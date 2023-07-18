<?php

namespace App\Controller;

use App\Form\ForgotPasswordType;
use App\Form\ResetPasswordType;
use App\Repository\UserRepository;
use App\Service\SendMailService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_front');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
       // throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
    #[Route(path: '/forgotPassword', name: 'app_forgot_passowrd')]
    public function forgottenPwd(SendMailService $mail, EntityManagerInterface $em,TokenGeneratorInterface $tg,Request $request,UserRepository $ur): Response
    {
        $form=$this->createForm(ResetPasswordType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $user=$ur->findOneByEmail($form->get('email')->getData());
            if($user)
            {
                $token=$tg->generateToken();
                $user->setResetToken($token);
                $em->persist($user);
                $em->flush();

                $url=$this->generateUrl('app_reset_pwd',['token'=>$token]
                ,UrlGeneratorInterface::ABSOLUTE_URL);
                //on crée les donnés de mail
                $context=compact('url','user');
                //envoi mail
                $mail->send(
                    'no-reply@e-Tech.tn',
                $user->getEmail(),
                'Reset Password',
                'password_reset',
                $context

                );
                $this->addFlash('success','Email sent successfully');
                return $this->redirectToRoute('app_login');

            }
            $this->addFlash('Danger','Invalid Email');
             return $this->redirectToRoute('app_forgot_passowrd');
        }
        return $this->render('security/reset_password_request.html.twig',[
            'form'=>$form
        ]);
    }
    #[Route(path: '/reset_pwd/{token}', name: 'app_reset_pwd')]
    public function reset(UserPasswordHasherInterface $uh, EntityManagerInterface $em,TokenGeneratorInterface $tg,Request $request,UserRepository $ur,$token): Response
    {
        // on  verifie si on a ce token dans la bd
        $user=$ur->findOneByResetToken($token);
        if($user){
            $form=$this->createForm(ForgotPasswordType::class);
            $form->handleRequest($request);
            
            if ($form->isSubmitted() && $form->isValid()) { 
                //on efface le token 
                $user->setResetToken('');
                $user->setPassword(
                    $uh->hashPassword(
                        $user,
                        $form->get('password')->getData()
                    )
                );
                     $ur->save($user, true);

                $this->addFlash(
                   'success',
                   'Password was changed successfully ! '
                );
                return $this->redirectToRoute('app_login');

            }
            return $this->render('security/reset_password.html.twig',[
                'form'=>$form
            ]);
        } 
        $this->addFlash('Danger','Invalid Token');
        return $this->redirectToRoute('app_login');

    }

}
