<?php

namespace App\Controller;

use App\Form\EditProfileType;
use App\Repository\OrderRepository;
use App\Repository\PublicationRepository;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;


class AccountController extends AbstractController
{
    #[Route('/account', name: 'app_account')]
    public function index(ReservationRepository $reservationRepository, PublicationRepository $publicationRepository, OrderRepository $orderRepository): Response
    {

        return $this->render('account/index.html.twig', [
            "reservations" => $reservationRepository->findBy(['resa_user' => $this->getUser()]),
            'publications' => $publicationRepository->findPublicationsByUserIdDescendingOrder($this->getUser()->getId()),
            'commandes' => $orderRepository->findOrdersByUserIdDescendingOrder($this->getUser()->getId()),
        ]);
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
            $user->setUpdatedAt(New \DateTimeImmutable());
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Votre profil a bien été mis à jour');

            return $this->redirectToRoute('app_account');
        }
        return $this->render('account/editprofile.html.twig', [
            'editForm' => $editForm->createView(),
        ]);
    }

    #[Route('/account/current/edit/reservation/{id}', name: 'app_account_save_current_edit_reservation')]
    public function saveEditReservation($id, SessionInterface $session): Response
    {
        $session->get('currentEditReservationId');
        $session->set('currentEditReservationId', $id);
        return $this->redirectToRoute('app_booking');
    }

}
