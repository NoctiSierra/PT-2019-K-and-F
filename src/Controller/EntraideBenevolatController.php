<?php

namespace App\Controller;

use App\Entity\Fiche;
use App\Form\FicheType;
use App\Repository\FicheRepository;
use App\Repository\TypeFicheRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Knp\Component\Pager\PaginatorInterface;

class EntraideBenevolatController extends AbstractController
{
    /**
     * @Route("/entraide_benevolat", name="fiche_entraide_benevolat")
     */
    public function entraide_benevolat(
        Security $security
    )
    {

        //Empecher les non admins d'ecrire ou modifier des articles.
        $user = $security->getUser();
        if($user === null){
            return $this->redirectToRoute('home');
        }

        return $this->render('entraide_benevolat/entraide_benevolat.html.twig');
    }

     /**
     * @Route("/entraide_benevolat/fiche/benevole_talent", name="fiche_benevole_talent")
     */
    public function benevole_talent(
        FicheRepository $frepo,
        TypeFicheRepository $tfrepo,
        Security $security,PaginatorInterface $paginator,Request $request
    )
    {
        $user = $security->getUser();
        if($user === null){
            return $this->redirectToRoute('home');
        }

            $fiches = $paginator->paginate(
            $frepo->findByTypeFiche($tfrepo->findByNom("Bénévole/Talent")),
            $request->query->getInt('page',1),
            12
        );

        return $this->render('entraide_benevolat/fiche.html.twig', [
            'fiches' => $fiches
        ]);
    }

    /**
     * @Route("/entraide_benevolat/fiche/don", name="fiche_don")
     */
    public function don(
        FicheRepository $frepo,
        TypeFicheRepository $tfrepo,
        Security $security,PaginatorInterface $paginator,Request $request
    )
    {
        $user = $security->getUser();
        if($user === null){
            return $this->redirectToRoute('home');
        }

        $fiches = $paginator->paginate(
            $frepo->findByTypeFiche($tfrepo->findByNom("Don")),
            $request->query->getInt('page',1),
            12
        );

        return $this->render('entraide_benevolat/fiche.html.twig', [
            'fiches' => $fiches
        ]);
    }

    /**
     * @Route("/entraide_benevolat/fiche/projet", name="fiche_projet")
     */
    public function projet(
        FicheRepository $frepo,
        TypeFicheRepository $tfrepo,
        Security $security,PaginatorInterface $paginator,Request $request
    )
    {
        $user = $security->getUser();
        if($user === null){
            return $this->redirectToRoute('home');
        }

        $fiches = $paginator->paginate(
            $frepo->findByTypeFiche($tfrepo->findByNom("Projet")),
            $request->query->getInt('page',1),
            12
        );

        return $this->render('entraide_benevolat/fiche.html.twig', [
            'fiches' => $fiches
        ]);
    }

    /**
     * @Route("/entraide_benevolat/fiche/montrer/{id}", name="fiche_show")
     */
    public function show(
        Fiche $fiche,
        Security $security
    )
    {
        $user = $security->getUser();
        if($user === null){
            return $this->redirectToRoute('home');
        }

        return $this->render('entraide_benevolat/show.html.twig', [
            'fiche' => $fiche
        ]);
    }

    /**
     * @Route("/entraide_benevolat/fiche/nouvelle_fiche", name="fiche_create")
     * @Route("/entraide_benevolat/fiche/editer/{id}", name="fiche_edit")
     */
    public function form(
        Fiche $fiche = null,
        Request $request,
        EntityManagerInterface $manager,
        Security $security
    )
    {
        $user = $security->getUser();
        
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if(!$fiche){
            $fiche = new Fiche();
        } else if($user !== $fiche->getAuthor() && !$user->isGranted('ROLE_ADMIN')){
            return $this->redirectToRoute('fiche_show', ['id' => $fiche->getId()]);
        }

        $form = $this->createForm(FicheType::class, $fiche);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            if(!$fiche->getId()){
                $fiche->setAuthor($user);
                $fiche->setCreatedAt(new \DateTime());
            }

            $manager->persist($fiche);
            $manager->flush();

            return $this->redirectToRoute('fiche_show', ['id' => $fiche->getId()]);
        }

        return $this->render('entraide_benevolat/create.html.twig', [
            'formFiche' => $form->createView(),
            'editMode' =>$fiche->getId() !== null
        ]);
    }

    /**
    * @Route("/entraide_benevolat/supprimer/{id}",name="fiche_delete")
    */
    public function delete(
        Fiche $fiche,
        EntityManagerInterface $manager,
        Security $security
    )
    {
        //Empecher les utilisateurs qui sont ni admin ni auteur de la fiche de la supprimmer.
        $user = $security->getUser();
        if($user === null || (!$user->isGranted("ROLE_ADMIN") && $user !== $fiche->getAuthor())){
            return $this->redirectToRoute('fiche_show',['id' => $fiche->getId()]);
        }
        
        $manager->remove($fiche);
        $manager->flush();
        return $this->redirectToRoute('fiche_entraide_benevolat');
    }
}
