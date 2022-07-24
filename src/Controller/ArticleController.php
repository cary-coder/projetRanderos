<?php

namespace App\Controller;
use App\Repository\ArticleRepository;
use App\Repository\CategorieRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ArticleController extends AbstractController
{


    //*****ROUTE POUR OBTENIR UN ARTICLES COTÉ USER************/

    #[Route('/article/{id<\d+>}', name: 'article_show')]
    public function show($id, ArticleRepository $repo): Response
    {
        $article =$repo-> find($id);

        return $this->render("article/showArticle.html.twig",[
            'article' => $article
        ]);
    }
  //*****ROUTE POUR OBTENIR TOUS LES ARTICLES COTÉ USER************/
    #[Route('/articles', name: 'articles_all')]
    public function all(ArticleRepository $repoArt, CategorieRepository $repoCat): Response
    {
        $articles = $repoArt->findAll();
        $categories = $repoCat-> findAll();

        return $this-> render("article/allArticles.html.twig",[
            'articles' => $articles,
            'categories' => $categories
        ]);
    }

    //*****ROUTE POUR OBTENIR TOUTES LES CATEGORIES ET SES ARTICLES ASSOCIÉS COTÉ USER************/

  #[Route('/categorie-{id<\d+>}', name: 'articles_categorie')]
  
 public function categorieArticles($id, CategorieRepository $repo)
 {
       // on recupere la categorie sur laquelle on a cliqué
       $categorie =$repo->find($id);
       $categories = $repo->findAll();

       //on recupere la liste de toutes les categories
       return $this->render('article/allArticles.html.twig', [
            'articles' =>  $categorie->getArticles(),
            'categories' => $categories
       ]);
 }
}
