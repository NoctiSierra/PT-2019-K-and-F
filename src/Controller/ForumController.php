<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\Message;
use App\Entity\SousCategorie;
use App\Entity\Topic;
use App\Form\AddSousCategorieType;
use App\Form\CategorieType;
use App\Form\MessageType;
use App\Form\SousCategorieType;
use App\Form\TopicType;
use App\Repository\CategorieRepository;
use App\Repository\MessageRepository;
use App\Repository\SousCategorieRepository;
use App\Repository\TopicRepository;
use App\Repository\UtilisateurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class ForumController extends AbstractController
{   
    /**
     * @Route("/forum/test", name="test")
     */
    public function test(CategorieRepository $catRepo) {
        $categorie = $catRepo->findOneBy([
            'id' => 62,
        ]);
        return $this->render('forum/test.html.twig', [
            'categorie' => $categorie,
        ]);
    }


    /**
     * Accueil du Forum:
     * Affichage des catégories et sous-catégories associées à celles-ci.
     * Possibilité aux admins d'ajouter des catégories.
     * 
     * @Route("/forum", name="home_forum")
     */
    public function homeForum(
        CategorieRepository $catRepo,
        Request $request,
        PaginatorInterface $paginator
    )
    {
        // Récupération de l'ensemble des catégories
        $donnees = $catRepo->findAll();

        // Création d'une pagination pour les catégories
        $categories = $paginator->paginate(
            $donnees, // Les catégories récupérées précédemment
            $request->query->getInt('page', 1), // Numéro de la page
            10 // Nombre d'éléments par page
        );

        return $this->render('forum/home.html.twig', [
            'categories' => $categories,
        ]);
    }


    /**
     * Ajout d'une catégorie
     * Formulaire d'une catégorie: titre, description et une première sous-catégorie
     * 
     * @Route("/forum/ajouter_une_categorie", name="add_categorie")
     */
    public function addCategorie(
        Request $request,
        CategorieRepository $catRepo
    )
    { 
        // Création d'une nouvelle catégorie et sous-catégorie
        $newCat = new Categorie();
        $newSousCat = new SousCategorie();

        // Création du formulaire
        $form = $this->createForm(AddSousCategorieType::class, $newSousCat);
        $form->handleRequest($request);

        // Une fois le formulaire validé
        if($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $this->getDoctrine()->getManager();

            $newCat = $newSousCat->getIdCategorie();

            $findNewCat = $catRepo->findOneBy([
                'nom' => $newCat->getNom(),
            ]);

            if(isset($findNewCat)) {
                $this->addFlash('error', 'Cette catégorie est déjà existante !');
            }
            else {
                $entityManager->persist($newCat);
                $entityManager->persist($newSousCat);

                $entityManager->flush();

                // Message de confirmation
                $this->addFlash('success', 'L\'ajout a bien été pris en compte !');

                return $this->redirectToRoute('home_forum');
            }
        }

        return $this->render('forum/categorie-add.html.twig', [
            'form' => $form->createView()
        ]);
    }


    /**
     * Modification d'une catégorie
     * Forumulaire d'une catégorie pré-rempli
     * 
     * @Route("/forum/{nomCat}/modifier_une_categorie", name="edit_categorie")
     */
    public function editCategorie(
        Request $request,
        CategorieRepository $catRepo,
        $nomCat
    )
    {
        // Récupération de la catégorie
        $categorie = $catRepo->findOneBy(['nom' => $nomCat]);
        
        // Création du formulaire
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        // Formulaire validé
        if($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($categorie);

            $entityManager->flush();

            // Message de confirmation
            $this->addFlash('success', 'La modification a bien été prise en compte !');

            return $this->redirectToRoute('show_categorie', [
                'nomCat' => $categorie->getNom()
            ]);
        }

        return $this->render('forum/categorie-edit.html.twig', [
            'categorie' => $categorie,
            'form' => $form->createView()
        ]);
    }


    /**
     * Supression d'une catégorie
     * 
     * @Route("/forum/{nomCat}/supprimer_une_categorie", name="delete_categorie")
     */
    public function deleteCategorie(
        CategorieRepository $catRepo,
        $nomCat
    )
    {
        // Récupération d'une catégorie
        $categorie = $catRepo->findOneBy(['nom' => $nomCat]);
        
        // On s'assure que la catégorie existe
        if($categorie != null) {
            $entityManager = $this->getDoctrine()->getManager();
            
            // On récupère toutes les sous-catégories de cette catégorie
            $sousCategories = $categorie->getSousCategories();

            // On supprime les liens entre catégorie/sous-catégorie/topics
            for($i=0 ; $i < count($sousCategories) ; $i++) {
                $topics = $sousCategories[$i]->getTopics();
                for ($j=0 ; $j < count($topics) ; $j++) {
                    $sousCategories[$i]->removeTopic($topics[$j]);
                }
                $categorie->removeSousCategory($sousCategories[$i]);
            }

            $entityManager->remove($categorie);
            $entityManager->flush();

            return $this->redirectToRoute('home_forum');
        }
        
        return $this->render('forum/home.html.twig');
    }


    /**
     * Ajouter une sous-catégorie
     * Formulaire à compléter: nom
     * 
     * @Route("/forum/{nomCat}/ajouter_une_sous_categorie", name="add_sous_categorie")
     */
    public function addSousCategorie(
        Request $request,
        CategorieRepository $catRepo,
        $nomCat
    )
    {
        // Récupération de la catégorie
        $categorie = $catRepo->findOneBy(['nom' => $nomCat]);
        
        // Création d'une nouvelle sous-catégorie
        $newSousCat = new SousCategorie();

        // Création du formulaire pour l'ajout de la sous-catégorie
        $form = $this->createForm(SousCategorieType::class, $newSousCat);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $this->getDoctrine()->getManager();

            $newSousCat->setIdCategorie($categorie);

            $entityManager->persist($newSousCat);

            $entityManager->flush();

            // Message de confirmation
            $this->addFlash('success', 'L\'ajout a bien été pris en compte !');

            return $this->redirectToRoute('show_categorie', [
                'nomCat' => $nomCat
            ]);
        }

        return $this->render('forum/sousCategorie-add.html.twig', [
            'categorie' => $categorie,
            'form' => $form->createView()
        ]);
    }


    /**
     * Modifier une sous-catégorie
     * Formulaire pré-rempli
     * 
     * @Route("/forum/{nomCat}/{nomSousCat}/modifier_une_sous_categorie", name="edit_sous_categorie")
     */
    public function editSousCategorie(
        Request $request,
        CategorieRepository $catRepo,
        SousCategorieRepository $sousCatRepo,
        $nomCat,
        $nomSousCat
    )
    {
        // Récupération de la catégorie
        $categorie = $catRepo->findOneBy(['nom' => $nomCat]);

        // Récupération de la sous-catégorie
        $sousCategorie = $sousCatRepo->findOneBy([
            'idCategorie' => $categorie,
            'nom' => $nomSousCat
        ]);

        // Création du formulaire pré-rempli
        $form = $this->createForm(SousCategorieType::class, $sousCategorie);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($sousCategorie);

            $entityManager->flush();

            // Message de confirmation
            $this->addFlash('success', 'La modification a bien été prise en compte !');

            return $this->redirectToRoute('show_categorie', [
                'nomCat' => $nomCat
            ]);
        }

        return $this->render('forum/sousCategorie-edit.html.twig', [
            'categorie' => $categorie,
            'sousCategorie' => $sousCategorie,
            'form' => $form->createView()
        ]);
    }


    /**
     * Suppression d'une sous-catégorie
     * 
     * @Route("/forum/{nomCat}/{nomSousCat}/supprimer_une_sous_categorie", name="delete_sous_categorie")
     */
    public function deleteSousCategorie(
        CategorieRepository $catRepo,
        SousCategorieRepository $sousCatRepo,
        TopicRepository $topicRepo,
        $nomCat,
        $nomSousCat
    )
    {
        // Récupération de la catégorie
        $categorie = $catRepo->findOneBy(['nom' => $nomCat]);

        // Récupération de la sous-catégorie
        $sousCategorie = $sousCatRepo->findOneBy([
            'idCategorie' => $categorie,
            'nom' => $nomSousCat
        ]);
        
        // Récupération des topics
        $topics = $topicRepo->findBy(['idSousCategorie' => $sousCategorie]);
        
        // On s'assure que la sous-catégorie existe
        if(($sousCategorie != null)) {
            $entityManager = $this->getDoctrine()->getManager();

            // On retire toutes les relations entre sous-catégorie/topic
            for ($i=0 ; $i<count($topics) ; $i++) {
                $sousCategorie->removeTopic($topics[$i]);
            }

            // Suppression de la sous-catégorie
            $categorie->removeSousCategory($sousCategorie);

            $entityManager->flush();

            // Message de confirmation
            $this->addFlash('success', 'La suppression a bien été prise en compte !');

            return $this->redirectToRoute('show_categorie', [
                'nomCat' => $nomCat
            ]);
        }
    }


    /**
     * Gestion d'une catégorie
     * 
     * @Route("/forum/{nomCat}", name="show_categorie")
     */
    public function showCategorie(
        CategorieRepository $catRepo,
        SousCategorieRepository $sousCatRepo,
        $nomCat
    )
    {
        // Récupération d'une catégorie
        $categorie = $catRepo->findOneBy(['nom' => $nomCat]);

        // Récupération d'une sous-catégorie
        $sousCategories = $sousCatRepo->findBy(['idCategorie' => $categorie]);
        
        return $this->render('forum/categorie-show.html.twig', [
            'categorie' => $categorie,
            'sousCategories' => $sousCategories
        ]);
    }


    /**
     * Liste des topics d'une sous-catégorie
     * 
     * @Route("/forum/{nomCat}/{nomSousCat}", name="list_topics")
     */
    public function listTopics(
        CategorieRepository $catRepo,
        SousCategorieRepository $sousCatRepo,
        TopicRepository $topicRepo,
        Request $request,
        PaginatorInterface $paginator,
        $nomCat,
        $nomSousCat
    )
    {
        // Récupération de la catégorie
        $categorie = $catRepo->findOneBy(['nom' => $nomCat]);

        // Récupération de la sous-catégorie
        $sousCategorie = $sousCatRepo->findOneBy([
            'idCategorie' => $categorie,
            'nom' => $nomSousCat
        ]);
        
        // Récupération des topics
        $donnees = $topicRepo->findBy(['idSousCategorie' => $sousCategorie]);
        
        // Paginer les topics
        $topics = $paginator->paginate(
            $donnees, // Topics à paginer
            $request->query->getInt('page', 1), // Commencement de la pagination
            10 // Nombre d'éléments par page
        );

        return $this->render('forum/topic-all.html.twig', [
            'topics' => $topics,
            'categorie' => $categorie,
            'sousCategorie' => $sousCategorie
        ]);
    }


    /**
     * Ajouter un Topic
     * 
     * @Route("/forum/{nomCat}/{nomSousCat}/ajouter_un_topic", name="add_topic")
     */
    public function addTopic(
        Request $request,
        CategorieRepository $catRepo,
        SousCategorieRepository $sousCatRepo,
        $nomCat,
        $nomSousCat
    )
    {
        // Récupération de la catégorie
        $categorie = $catRepo->findOneBy(['nom' => $nomCat]);

        // Récupération de la sous-catégorie
        $sousCategorie = $sousCatRepo->findOneBy([
            'idCategorie' => $categorie,
            'nom' => $nomSousCat
        ]);
        
        // Récupération de l'utilisateur courant
        $currentUser = $this->getUser();
        
        // Création d'un nouveau topic
        $newTopic = new Topic();

        // Création d'un formulaire
        $form = $this->createForm(TopicType::class, $newTopic);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $this->getDoctrine()->getManager();

            $newTopic   ->setIdUtilisateur($currentUser)
                        ->setDateHeureCreation(new \DateTime())
                        ->setIdSousCategorie($sousCategorie);

            $entityManager->persist($newTopic);
            $entityManager->flush();

            // Message de confirmation
            $this->addFlash('success', 'L\'ajout d\'un Topic a bien été pris en compte !');

            return $this->redirectToRoute('show_topic', [
                'nomCat' => $nomCat,
                'nomSousCat' => $nomSousCat,
                'titreTopic' => $newTopic->getTitre()
            ]);
        }

        return $this->render('forum/topic-add.html.twig', [
            'categorie' => $categorie,
            'sousCategorie' => $sousCategorie,
            'currentUser' => $currentUser,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Voir le contenu d'un topic
     * 
     * @Route("/forum/{nomCat}/{nomSousCat}/{titreTopic}", name="show_topic")
     */
    public function showTopic(
        Request $request,
        PaginatorInterface $paginator,
        CategorieRepository $catRepo,
        SousCategorieRepository $sousCatRepo,
        TopicRepository $topicRepo,
        MessageRepository $messageRepo,
        $nomCat,
        $nomSousCat,
        $titreTopic
    )
    {
        // Récupération de la catégorie
        $categorie = $catRepo->findOneBy(['nom' => $nomCat]);

        // Récupération de la sous-catégorie
        $sousCategorie = $sousCatRepo->findOneBy([
            'idCategorie' => $categorie,
            'nom' => $nomSousCat
        ]);

        // Récupération du topic
        $topic = $topicRepo->findOneBy([
            'titre' => $titreTopic,
            'idSousCategorie' => $sousCategorie
        ]);

        // Récupération le rédacteur du topic
        $user = $topic->getIdUtilisateur();

        // Récupération des messages
        $donneesMessages = $messageRepo->findBy([
            'idTopic' => $topic,
            'idDestinataire' => $topic->getIdUtilisateur(),
        ], array('dateEnvoi' => 'DESC'));

        // Récupération de l'utilisateur courant
        $currentUser = $this->getUser();

        // Pagination des messages
        $messages = $paginator->paginate(
            $donneesMessages,
            $request->query->getInt('page', 1),
            4
        );

        $i=0;
        $posteurs = null;
        $tmp = null;

        foreach($messages as $message) {
            $tmp[$i] = array([
                'posteur' => $message->getIdPosteur(),
                'topic' => $message->getIdTopic(),
            ]);
            $i++;
        }

        $posteurs = $this->division($tmp);

        return $this->render('forum/topic-show.html.twig', [
            'categorie' => $categorie,
            'sousCategorie' => $sousCategorie,
            'topic' => $topic,
            'user' => $user,
            'currentUser' => $currentUser,
            'messages' => $messages,
            'posteurs' => $posteurs,
            'tmp' => $tmp,
        ]);
    }


    /**
     * Modification d'un topic
     * Formulair pré-rempli
     * 
     * @Route("/forum/{nomCat}/{nomSousCat}/{titreTopic}/modifier", name="edit_topic")
     */
    public function editTopic(
        Request $request,
        CategorieRepository $catRepo,
        SousCategorieRepository $sousCatRepo,
        TopicRepository $topicRepo,
        $nomCat,
        $nomSousCat,
        $titreTopic
    )
    {
        // Récupération de l'utilisateur courant
        $currentUser = $this->getUser();

        // Récupération de la catégorie
        $categorie = $catRepo->findOneBy(['nom' => $nomCat]);

        // Récupération de la sous-catégorie
        $sousCategorie = $sousCatRepo->findOneBy([
            'idCategorie' => $categorie,
            'nom' => $nomSousCat
        ]);

        // Récupération des topics
        $topic = $topicRepo->findOneBy([
            'titre' => $titreTopic,
            'idSousCategorie' => $sousCategorie,
            'idUtilisateur' => $currentUser
        ]);

        // Création d'un formulaire
        $form = $this->createForm(TopicType::class, $topic);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $this->getDoctrine()->getManager();

            $topic  ->setDateHeureCreation(new \DateTime());

            $entityManager->persist($topic);
            $entityManager->flush();

            // Message de confirmation
            $this->addFlash('success', 'Vos modifications ont bien été prises en compte !');

            return $this->redirectToRoute('show_topic', [
                'nomCat' => $nomCat,
                'nomSousCat' => $nomSousCat,
                'titreTopic' => $topic->getTitre()
            ]);
        }

        return $this->render('forum/topic-edit.html.twig', [
            'categorie' => $categorie,
            'sousCategorie' => $sousCategorie,
            'topic' => $topic,
            'currentUser' => $currentUser,
            'form' => $form->createView(),
        ]);

    }


    /**
     * Suppression d'un topic
     * 
     * @Route("/forum/{nomCat}/{nomSousCat}/{titreTopic}/supprimer", name="delete_topic")
     */
    public function deleteTopic(
        CategorieRepository $catRepo,
        SousCategorieRepository $sousCatRepo,
        TopicRepository $topicRepo,
        $nomCat,
        $nomSousCat,
        $titreTopic
    )
    {
        // Récupération de l'utilisateur courant
        $currentUser = $this->getUser();

        // Récupération de la catégorie
        $categorie = $catRepo->findOneBy(['nom' => $nomCat]);

        // Récupération de la sous-catégorie
        $sousCategorie = $sousCatRepo->findOneBy([
            'idCategorie' => $categorie,
            'nom' => $nomSousCat
        ]);

        // Récupération du topic
        $topic = $topicRepo->findOneBy([
            'titre' => $titreTopic,
            'idSousCategorie' => $sousCategorie,
            'idUtilisateur' => $currentUser
        ]);

        // On s'assure que l'utilisateur courant et le topic existent
        if (($currentUser != null) && ($topic != null)) {
            $entityManager = $this->getDoctrine()->getManager();

            $currentUser->removeTopic($topic);
            $entityManager->flush();

            // Message de confirmation
            $this->addFlash('success', 'La suppression a bien été prise en compte !');

            return $this->redirectToRoute('list_topics', [
                'nomCat' => $nomCat,
                'nomSousCat' => $nomSousCat
            ]);
        }
    }


    /**
     * Résoudre un topic
     * 
     * @Route("/forum/{nomCat}/{nomSousCat}/{titreTopic}/résoudre", name="resolve_topic")
     */
    public function resolveTopic(
        CategorieRepository $catRepo,
        SousCategorieRepository $sousCatRepo,
        TopicRepository $topicRepo,
        $nomCat,
        $nomSousCat,
        $titreTopic
    )
    {
        // Récupération de la catégorie
        $categorie = $catRepo->findOneBy(['nom' => $nomCat]);

        // Récupération de la sous-catégorie
        $sousCategorie = $sousCatRepo->findOneBy([
            'idCategorie' => $categorie,
            'nom' => $nomSousCat
        ]);

        // Récupération du topic
        $topic = $topicRepo->findOneBy([
            'titre' => $titreTopic,
            'idSousCategorie' => $sousCategorie
        ]);

        // On s'assure que le topic existe
        if ($topic != null) {
            $entityManager = $this->getDoctrine()->getManager();

            // On retire la relation entre le topic et sa sous-catégorie
            $sousCategorie->removeTopic($topic);
            $entityManager->flush();

            // Message de confirmation
            $this->addFlash('success', 'La résolution a bien été prise en compte !');

            return $this->redirectToRoute('list_topics', [
                'nomCat' => $nomCat,
                'nomSousCat' => $nomSousCat
            ]);
        }
    }


    /**
     * Répondre à un topic
     * 
     * @Route("/forum/{nomCat}/{nomSousCat}/{titreTopic}/{pseudoTarget}/contacter", name="answer_topic")
     */
    public function answerTopic(
        Request $request,
        CategorieRepository $catRepo,
        SousCategorieRepository $sousCatRepo,
        TopicRepository $topicRepo,
        MessageRepository $messageRepo,
        UtilisateurRepository $utilisateurRepo,
        $nomCat,
        $nomSousCat,
        $titreTopic,
        $pseudoTarget
    )
    {
        // Récupération de la catégorie
        $categorie = $catRepo->findOneBy(['nom' => $nomCat]);

        // Récupération de la sous-catégorie
        $sousCategorie = $sousCatRepo->findOneBy([
            'idCategorie' => $categorie,
            'nom' => $nomSousCat
        ]);

        // Récupération du topic
        $topic = $topicRepo->findOneBy([
            'titre' => $titreTopic,
            'idSousCategorie' => $sousCategorie
        ]);

        // Récupération des messages
        $messages = $topic->getMessages();

        // Récupération du rédacteur du topic
        $target = $utilisateurRepo->findOneBy(['login' => $pseudoTarget]);

        //$creator = $topic->getIdUtilisateur();

        // Récupération de l'utilisateur courant
        $currentUser = $this->getUser();

        // Création d'un nouveau message
        $newMessage = new Message();

        // Création d'un formulaire
        $form = $this->createForm(MessageType::class, $newMessage);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();

            $newMessage ->setIdDestinataire($target)
                        ->setIdPosteur($currentUser)
                        ->setIdTopic($topic)
                        ->setDateEnvoi(new \DateTime());

            $entityManager->persist($newMessage);
            $entityManager->flush();

            // Message de confirmation
            $this->addFlash('success', 'Votre message a bien été envoyé !');

            return $this->redirectToRoute('answer_topic', [
                'nomCat' => $nomCat,
                'nomSousCat' => $nomSousCat,
                'titreTopic' => $titreTopic,
                'pseudoTarget' => $pseudoTarget
            ]);
        }

        return $this->render('forum/topic-answer.html.twig', [
            'categorie' => $categorie,
            'sousCategorie' => $sousCategorie,
            'topic' => $topic,
            'messages' => $messages,
            //'creator' => $creator,
            'currentUser' => $currentUser,
            'target' => $target,
            'form' => $form->createView()
        ]);
    }

    public function division($tmp) {
        $i=0;
        $k=1;

        if($tmp != null) {
            for ($i=0 ; $i<=count($tmp)-1 ; $i++) {
                if($i == 0) {
                    $posteurs[$i] = $tmp[$i];
                }
                elseif($i == count($tmp)-1) {
                    if ($tmp[$i-1] != $tmp[$i]) {
                        $posteurs[$k] = $tmp[$i];
                        $k++;
                    }
                }
                else {
                    if ($tmp[$i] != $tmp[$i-1]) {
                        $posteurs[$k] = $tmp[$i];
                        $k++;
                    }
                }
            }
            return $posteurs;
        }
    }

    /*public function getComments(
        TopicRepository $topicRepo,
        $titreTopic
    )
    {
        $topic = $topicRepo->findOneBy(['titre' => $titreTopic]);
        return new JsonResponse([
            'comments' => $topic->getMessages()
        ]);
    }

    public function addComment(
        Request $request,
        CategorieRepository $catRepo,
        SousCategorieRepository $sousCatRepo,
        TopicRepository $topicRepo,
        MessageRepository $messageRepo,
        UtilisateurRepository $utilisateurRepo,
        $nomCat,
        $nomSousCat,
        $titreTopic,
        $pseudoTarget
    )
    {
        // Récupération de la catégorie
        $categorie = $catRepo->findOneBy(['nom' => $nomCat]);

        // Récupération de la sous-catégorie
        $sousCategorie = $sousCatRepo->findOneBy([
            'idCategorie' => $categorie,
            'nom' => $nomSousCat
        ]);

        // Récupération du topic
        $topic = $topicRepo->findOneBy([
            'titre' => $titreTopic,
            'idSousCategorie' => $sousCategorie
        ]);

        // Récupération des messages
        $messages = $topic->getMessages();

        // Récupération du rédacteur du topic
        $target = $utilisateurRepo->findOneBy(['login' => $pseudoTarget]);

        //$creator = $topic->getIdUtilisateur();

        // Récupération de l'utilisateur courant
        $currentUser = $this->getUser();

        // Création d'un nouveau message
        $newMessage = new Message();

        // Création d'un formulaire
        $form = $this->createForm(MessageType::class, $newMessage);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();

            $newMessage ->setIdDestinataire($target)
                        ->setIdPosteur($currentUser)
                        ->setIdTopic($topic)
                        ->setDateEnvoi(new \DateTime());

            $entityManager->persist($newMessage);
            $entityManager->flush();

            // Message de confirmation
            $this->addFlash('success', 'Votre message a bien été envoyé !');

            return $this->redirectToRoute('answer_topic', [
                'nomCat' => $nomCat,
                'nomSousCat' => $nomSousCat,
                'titreTopic' => $titreTopic,
                'pseudoTarget' => $pseudoTarget
            ]);
        }
    }*/

}
