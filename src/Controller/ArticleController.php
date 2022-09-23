<?php

namespace App\Controller;
use App\Entity\Categorie;
use App\Repository\ArticleRepository;
use App\Repository\CategorieRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ArticleController extends AbstractController
{


    //*****ROUTE POUR OBTENIR UN ARTICLE COTÉ USER************/

    #[Route('/article/{id<\d+>}', name: 'article_show')]
    public function show($id, ArticleRepository $repo): Response
    {
        $article =$repo-> find($id);

        return $this->render("article/showArticle.html.twig",[
            'article' => $article
        ]);
    }
  //*****ROUTE POUR OBTENIR TOUS LES ARTICLES COTÉ USER Onglet Articles/Tous les Articles************/
  //*****IMPORTANT LE FILTRE "categorie.nom != Tenerife" se APPLIQUE dans TWIG********/

    #[Route('/articles', name: 'articles_all')]
    public function all(ArticleRepository $repoArt, CategorieRepository $repoCat): Response
    {
        $articles = $repoArt->findBy([], ["dateDeCreation"=>"DESC"]);
        $categories = $repoCat-> findAll();

        return $this-> render("article/allArticles.html.twig",[
            'articles' => $articles,
            'categories' => $categories
        ]);
    }

    //*****ROUTE POUR OBTENIR LES DERNIERS ARTICLES COTÉ USER Onglet Articles/Nouveautés************/
    //*****IMPORTANT LE FILTRE "categorie.nom != Tenerife" se APPLIQUE dans TWIG********/
    #[Route('/news', name: 'articles_derniers')]
    public function nouveautes(ArticleRepository $repoNews): Response
    {
        $derniersArticles= $repoNews->findBy([], ["dateDeCreation"=>"DESC"], 9);
   
        return $this->render('article/derniers.html.twig', [
            'articles' => $derniersArticles
        ]);
    }

//*****ROUTE POUR OBTENIR TOUS LES ARTICLES COTÉ USER par categorie TENERIFE DANS Onglet Articles************/
//*****IMPORTANT LE FILTRE "categorie.nom==Tenerife" se APPLIQUE dans TWIG********/

    #[Route('/tenerife', name: 'articles_tenerife')]
    public function allTenerife(ArticleRepository $repoArten, CategorieRepository $repoCaten): Response
    {
        $articles = $repoArten->findBy([], ["dateDeCreation"=>"DESC"]);
        $categorie = $repoCaten-> findAll();

        return $this-> render("tenerife/allTenerife.html.twig",[
            'articles' => $articles,
            'categorie' => $categorie
        ]);
    }

    
    //*****ROUTE POUR OBTENIR TOUTES LES CATEGORIES ET SES ARTICLES ASSOCIÉS COTÉ USER************/

  #[Route('/categorie-{id<\d+>}', name: 'articles_categorie')]
  
 public function categorieArticles($id, CategorieRepository $repo)
 {
       // on recupere la categorie sur laquelle on a cliqué
       $categorie =$repo->find($id);
       $categories = $repo->findAll();

       //on recupere la liste de toutes les articles des categories
       return $this->render('article/allArticles.html.twig', [
            'articles' =>  $categorie->getArticles(),
            'categories' => $categories
       ]);
 }
   
}
