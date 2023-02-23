<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\PublicationRepository;
use App\Repository\UserRepository;

use App\Form\EditPassType;
use App\Form\EditProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;



class AccountController extends AbstractController
{
    #[Route('/account', name: 'app_account')]
    public function index(): Response
    {
        return $this->render('account/index.html.twig');
    }



    #[Route('/account/edit', name: 'app_account_edit')]
    public function editProfile(EntityManagerInterface $em, Request $request): Response
    {
        //on récupère le user connecté
        $user = $this->getUser();

        //on crée le formulaire
        $editForm = $this->createForm(EditProfileType::class, $user );

        //on traite la requête du form
        $editForm->handleRequest($request);

        if($editForm->isSubmitted() && $editForm->isValid()){
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Votre profil a bien été mis à jour');

            return $this->redirectToRoute('app_account');
        }
        return $this->render('account/editprofile.html.twig', [
            'editForm' => $editForm->createView(),
        ]);
    }

    #[Route('/account', name: 'app_account')]
    public function showUserPublications(UserRepository $UserRepository)
    {
        $userId = $this->getPubliUser().getId();
        $publiUser = $this->getPublications();

        $publications = $this->getDoctrine()
            ->getRepository(PublicationRepository::class)
            ->findBy(['user' => $userId]);

        return $this->render('account/index.html.twig', [
            'publications' => $publications,
        ]);
    }








}
