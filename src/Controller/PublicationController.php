<?php

namespace App\Controller;

use App\Entity\Publication;
use App\Form\PublicationFormType;
use App\Repository\PublicationRepository;
use App\Service\UploadImageService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/publication', name: 'app_publication')]
class PublicationController extends AbstractController
{
    #[Route('/', name: '')]
    public function feed(PublicationRepository $publicationRepository):Response
    {
        return $this->render('publication/feed.html.twig', [
            'publications' => $publicationRepository->findAllByDescendingOrder(),
        ]);
    }

    #[Route('/ajout', name: '_add')]
    public function add(Request $request, EntityManagerInterface $entityManager, UploadImageService $uploadImgService): Response
    {
        //on crée une nouvelle publication
        $publi = new Publication();

        //on crée le formulaire
        $publiForm = $this->createForm(PublicationFormType::class, $publi);

        //on traite la requête du form
        $publiForm->handleRequest($request);

        //on vérifie si le form est soumis et valide
        if($publiForm->isSubmitted() && $publiForm->isValid()){
            //on récupère l'user
            $user = $this->getUser();
            $publi->setPubliUser($user);

            //on récupère la photo
            $photo = $publiForm->get('photo')->getData();

            //on appelle le service upload image
            try{
                $fichier = $uploadImgService->create($photo,300,300);

                //on set le nom de fichier dans la bdd
                $publi->setPhoto($fichier);

                $entityManager->persist($publi);
                $entityManager->flush();

                $this->addFlash('success', 'Publication partagée avec succès.');

                return $this->redirectToRoute('app_publication');
            }
            catch (Exception){
                $this->addFlash('danger', 'Format d\'image incorrect.');
            }

        }

        return $this->render('publication/add.html.twig', [
            'publiForm' => $publiForm,
            ]);
    }

    #[Route('/modification/{id}', name: '_update')]
    public function update(int $id, Request $request, EntityManagerInterface $entityManager, PublicationRepository $publicationRepository): Response
    {
        //on récupère la publication
        $publi = $publicationRepository->findPublicationById($id);

        //on crée le formulaire
        $publiForm = $this->createForm(PublicationFormType::class, $publi);

        //on traite la requête du form
        $publiForm->handleRequest($request);

        //on vérifie si le form est soumis
        if($publiForm->isSubmitted()){
            $publi->setUpdatedAt(New \DateTimeImmutable());
            $entityManager->persist($publi);
            $entityManager->flush();

            $this->addFlash('success', 'Publication mise à jour avec succès');

            return $this->redirectToRoute('app_publication');
        }

        return $this->render('publication/update.html.twig', [
            'publiForm' => $publiForm,
            'publication' => $publicationRepository->findPublicationById($id),
        ]);
    }

    #[Route('/suppression/{id}', name: '_delete')]
    public function delete(int $id, EntityManagerInterface $entityManager, PublicationRepository $publicationRepository): Response
    {

        //on récupère la publication
        $publi = $publicationRepository->findPublicationById($id);

        try{
            $entityManager->remove($publi);
            $entityManager->flush();
            $this->addFlash('success', 'Publication supprimée avec succès');
            return $this->redirectToRoute('app_account');
        } catch (Exception){
            $this->addFlash('danger', 'Un problème est survenu.');
        }

        return $this->redirectToRoute('app_account');
    }
}
