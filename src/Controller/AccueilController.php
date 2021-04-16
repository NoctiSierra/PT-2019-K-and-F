<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AccueilController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function home(
        ArticleRepository $articleRepo
    )
    {
        $articles = $articleRepo->findAll();

        return $this->render('accueil/home.html.twig', [
            'articles' => $articles
        ]);
    }
}
