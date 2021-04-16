<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Knp\Component\Pager\PaginatorInterface;

class ActuController extends AbstractController
{
    /**
     * @Route("/actu/", name="actu")
     */
    public function index(ArticleRepository $repo,PaginatorInterface $paginator,Request $request)
    {

        $articles = $paginator->paginate(
            $repo->findBy([], ['date' => 'DESC']),
            $request->query->getInt('page',1),
            12
        );

        return $this->render('actu/index.html.twig', [
            'articles' => $articles
        ]);
    }

    /**
    * @Route("/actu/newArticle", name="createarticle")
    * @Route("/actu/{id}/edition", name="editionarticle")
    */
    public function article(
        Article $article = null,
        Request $request,
        EntityManagerInterface $manager,
        Security $security
    )
    {
        //Empecher les non admins d'ecrire ou modifier des articles.
        $user = $security->getUser();
        if($user === null || !$user->isGranted("ROLE_ADMIN")){
            return $this->redirectToRoute('actu');
        }

        if (!$article) {
            $article=new Article();
        }
        
        $form = $this->createFormBuilder($article)
                ->add('titre', TextType::class)
                ->add('contenu',TextareaType::class)
                ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$article->getId()) {
                $article->setDate(new \DateTime());
            }
            
            $manager->persist($article);
            $manager->flush();

            return $this->redirectToRoute('showarticle',['index' => $article->getId()]);
        }

        return $this->render('actu/createarticle.html.twig', [
            'formArticle' => $form->createView(),
            'modeEdition' => $article->getId() != null
        ]);
    }

    /**
    * @Route("/actu/{index}", name="showarticle")
    */
    public function show($index)
    {
    	$repo = $this->getDoctrine()->getRepository(Article::class);
    	$article = $repo->find($index);

        return $this->render('actu/showarticle.html.twig',[
        	'article' => $article
        ]);
    }

    /**
    * @Route("/actu/Supprimer/{id}",name="supprimerarticle")
    */
    public function removeIt(
        Article $article,
        EntityManagerInterface $manager,
        ArticleRepository $repo,
        Security $security
    )
    {
        //Empecher les non admins de supprimmer des articles.
        $user = $security->getUser();
        if($user === null || !$user->isGranted("ROLE_ADMIN")){
            return $this->redirectToRoute('showarticle',['index' => $article->getId()]);
        }
        
        $manager->remove($article);
        $manager->flush();
        $articles = $repo->findAll();
        return $this->redirectToRoute('actu',['articles' => $articles]);
    }
}
