<?php

namespace App\Controller;

use App\Form\ResetPasswordFormType;
use App\Form\ResetPasswordRequestFormType;
use App\Repository\UserRepository;
use App\Service\SecurityServices;
use App\Service\SendMailService;
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
    private SecurityServices $securityServices;

    public function __construct(SecurityServices $securityServices)
    {
        $this->securityServices = $securityServices;
    }

    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route('/forgotten-password', name:'app_forgotten_pwd')]
    public function forgottenPassword(Request $request, UserRepository $userRepository, TokenGeneratorInterface $tokenGenerator, EntityManagerInterface $entityManager, SendMailService $mail): Response
    {
        $form = $this->createForm(ResetPasswordRequestFormType::class);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            //on cherche l'user par son email
            $user = $userRepository->findOneBy(['email' => $form->get('email')->getData()]);

            // on vérifie si on a un user avec cet email
            if($user){
                // on génère un token de réinitialisation
                $token = $tokenGenerator->generateToken();
                $user->setResetToken($token);
                $entityManager->persist($user);
                $entityManager->flush();

                // on génère un lien de réinitialisation du mdp
                $url = $this->generateUrl('reset_pwd', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL); //url absolue = url complète

                // on envoie le mail
                $mail->send(
                    'no-reply@maps.com',
                    $user->getEmail(),
                    'Réinitialisation du mot de passe',
                    'password_reset',
                    ['url' => $url, 'user' => $user]
                );

                $this->addFlash('success', 'Email envoyé avec succès.');
                return $this->redirectToRoute('app_login');
            }
            // si $user est null => pas d'user avec cet email
            $this->addFlash('danger', 'Cet email ne correspond à aucun compte MAPS.');
            return $this->redirectToRoute('app_forgotten_pwd');
        }

        return $this->render('security/reset_password_request.html.twig', [
            'requestPasswordForm' => $form->createView()
        ]);
    }

    #[Route('/forgotten-password/{token}', name:'reset_pwd')]
    public function resetPass(string $token, Request $request, UserRepository $userRepository, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        //dd(['token'=>$token]);
        // on vérifie si on a le token dans la base
        $user = $userRepository->findOneBy(['resetToken'=>$token]);

        // on vérifie si on a un user avec ce token
        if($user){
            $form = $this->createForm(ResetPasswordFormType::class);
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()){
                $this->securityServices->resetPassword($user, $entityManager, $passwordHasher, $form);
                $this->addFlash('success', 'Mot de passe changé avec succès.');
                return $this->redirectToRoute('app_login');
            }
            return $this->render('security/reset_password.html.twig', [
                'passwordForm' => $form->createView()
            ]);
        }
        //si $user est nul => pas d'user avec ce token
        $this->addFlash('danger', 'Un problème est survenu...');
        return $this->redirectToRoute('app_forgotten_pwd');
    }
}
