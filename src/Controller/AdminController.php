<?php

namespace App\Controller;
use DateTime;
use App\Entity\Images;
use App\Entity\Article;
use App\Entity\Categorie;
use App\Form\ArticleType;
use App\Form\CategorieType;
use App\Repository\ImagesRepository;
use App\Repository\ArticleRepository;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


//*********************ROUTES ADMIN CRUD ARTICLES ET CATEGORIES*****************/

#[Route('/admin', name: 'admin_')]

class AdminController extends AbstractController
{
    #[Route('/ajout-article', name: 'ajout_article')]
    public function new(Request $request, EntityManagerInterface $manager, SluggerInterface $slugger): Response
    {
                //code pour vérifier l'autentification, si l'user n'est pas connecté
    if( !$this->isGranted('IS_AUTHENTICATED_FULLY')){
         $this->addFlash('error', "Veuillez vous connecter pour acceder à cette page!");
         return $this->redirectToRoute('app_login');
        }
        // si l'utilisateur est connecté mais n'est pas admin
    if(!$this->isGranted("ROLE_ADMIN")){
        $this->addFlash('error', "Vous êtes un intrus, vous ne pouvez pas vous connecter à cette page!!!");
        return $this->redirectToRoute('app_home');
    }
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ( $form->isSubmitted() && $form->isValid() ) {
            // on recupére les images transmises
                $images = $form->get('images')->getData();
            // on boucle sur les images
                foreach($images as $image)
            {

            //on cree un nouveu nom pour la nouvelle image
                $fileName = $slugger->slug($article->getTitre()) . uniqid() . '.' . $image->guessExtension();
            
            // on copie le fichier dans le dossier
                //on deplace l'image dans le dossier parametré dans service.yaml
            try{
                $image->move($this->getParameter('photos_articles'), $fileName);
            }catch(FileException $e){
                //gestion des erreurs upload image
            }
            // on stocke l'image (le nom) dans la BDD
            $img= new Images();
            $img->setName($fileName);
            $article->addImage($img);

            }
            //on récupère le manager de doctrine
            
            $article->setdateDeCreation(new DateTime("now"));
            // $manager = $this->getDoctrine()->getManager();
           
            $manager->persist($article);
            $manager->flush();
             $this->addFlash("success", "felicitations, votre article à bien été enregistré");
            /** return $this->redirectToRoute("admin_gestion_articles"); */
           
        }
         
        return $this->render('admin/formulaire.html.twig', [
            'formArticle' => $form->createView(),
        ]);
    }

    //**************ROUTE DETAILS ARTICLE PAR ID = SHOW DETAILS***************/
    #[Route('/article/{id<\d+>}', name: 'details_article')]
    public function show($id, ArticleRepository $repo): Response
    {
        $article =$repo-> find($id);

        return $this->render("admin/details-article.html.twig",[
            'article' => $article
        ]);
    }

    //***************ROUTE ALL ARTICLES = LISTER DANS UNE TABLE ADMIN******************/

    #[Route('/gestion-articles', name: 'gestion_articles')]
    public function gestionArticles(ArticleRepository $repo): Response
    {
                //code pour vérifier l'autentification, si l'user n'est pas connecté
    if( !$this->isGranted('IS_AUTHENTICATED_FULLY')){
         $this->addFlash('error', "Veuillez vous connecter pour acceder à cette page!");
         return $this->redirectToRoute('app_login');
        }
        // si l'utilisateur est connecté mais n'est pas admin
    if(!$this->isGranted("ROLE_ADMIN")){
        $this->addFlash('error', "Vous êtes un intrus, vous ne pouvez pas vous connecter à cette page!!!");
        return $this->redirectToRoute('app_home');
    }
        $articles = $repo->findAll();
  
        return $this-> render("admin/gestion-articles.html.twig",[
            'articles' =>$articles,
        ]);
    }

    //*****************ROUTE UPDATE ARTICLE PAR ID = EDITION*************/

   #[Route("/update-article-{id<\d+>}", name:"update_article")]
    public function update($id, ArticleRepository $repo, Request $request,  SluggerInterface $slugger)
    {
        //code pour vérifier l'autentification, si l'user n'est pas connecté
     if( !$this->isGranted('IS_AUTHENTICATED_FULLY')){
         $this->addFlash('error', "Veuillez vous connecter pour acceder à cette page!");
         return $this->redirectToRoute('app_login');
        }
        // si l'utilisateur est connecté mais n'est pas admin
    if(!$this->isGranted("ROLE_ADMIN")){
        $this->addFlash('error', "Vous êtes un intrus, vous ne pouvez pas vous connecter à cette page!!!");
        return $this->redirectToRoute('app_home');
    }
        $article = $repo->find($id);
        
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ( $form->isSubmitted() && $form->isValid() ) {
            // on recupére les images transmises
                $images = $form->get('images')->getData();
            // on boucle sur les images
                foreach($images as $image)
            {

            //on cree un nouveu nom pour la nouvelle image
                $fileName = $slugger->slug($article->getTitre()) . uniqid() . '.' . $image->guessExtension();
            
            // on copie le fichier dans le dossier
                //on deplace l'image dans le dossier parametré dans service.yaml
            try{
                $image->move($this->getParameter('photos_articles'), $fileName);
            }catch(FileException $e){
                //gestion des erreurs upload image
            }
            // on stocke l'image (le nom) dans la BDD
            $img= new Images();
            $img->setName($fileName);
            $article->addImage($img);

            }
            
                $repo->add($article, 1);
                $this->addFlash("success", "felicitations, votre article à bien été modifié");
                return $this->redirectToRoute("admin_gestion_articles");
             
        }
             
        return $this->render("admin/formulaire.html.twig", [
            'formArticle' => $form->createView()
        ]);
    }
    //**************ROUTE DELETE ARTICLE PAR ID**********/
      
    #[Route("/delete-article-{id<\d+>}", name:"delete_article")]
    
    public function delete($id, ArticleRepository $repo)
    {
        //code pour vérifier l'autentification, si l'user n'est pas connecté
     if( !$this->isGranted('IS_AUTHENTICATED_FULLY')){
         $this->addFlash('error', "Veuillez vous connecter pour acceder à cette page!");
         return $this->redirectToRoute('app_login');
        }
        // si l'utilisateur est connecté mais n'est pas admin
    if(!$this->isGranted("ROLE_ADMIN")){
        $this->addFlash('error', "Vous êtes un intrus, vous ne pouvez pas vous connecter à cette page!!!");
        return $this->redirectToRoute('app_home');
    }
        $article = $repo->find($id);
        
        $repo->remove($article, 1);
          $this->addFlash("success", "felicitations, votre article à bien été supprimé!");
        return $this->redirectToRoute("admin_gestion_articles");
    }

  //***********ROUTE DELETE IMAGE DE L'ARTICLE PAR ID ***************/

  #[Route("/delete-image-{id<\d+>}", name:"delete_image")]
    public function deleteImage($id, ImagesRepository $repo)
    {
        $image = $repo->find($id);
        $repo->remove($image, 1);

        return $this->redirectToRoute("admin_gestion_articles");

    }

    /**--------ADMIN - CRUD-GESTION DES CATEGORIES--------------*/
    /***********ROUTE AJOUT CATEGORIE *****************************/
    #[Route('/categorie-ajout', name: 'ajout_categorie')]

    public function ajoutCategorie(Request $request, CategorieRepository $repo, SluggerInterface $slugger, EntityManagerInterface $manager)
    { 
         //code pour vérifier l'autentification, si l'user n'est pas connecté
     if( !$this->isGranted('IS_AUTHENTICATED_FULLY')){
         $this->addFlash('error', "Veuillez vous connecter pour acceder à cette page!");
         return $this->redirectToRoute('app_login');
        }
        // si l'utilisateur est connecté mais n'est pas admin
    if(!$this->isGranted("ROLE_ADMIN")){
        $this->addFlash('error', "Vous êtes un intrus, vous ne pouvez pas vous connecter à cette page!!!");
        return $this->redirectToRoute('app_home');
    }
        $categorie = new Categorie();
        
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid())
        {
             $imageFile=$form->get('photoForm')->getData();
            
            //on cree un nouveu nom pour la nouvelle image
            $fileName = $slugger->slug($categorie->getNom()) . uniqid() . '.' . $imageFile->guessExtension();
            
            //on deplace l'image dans le dossier parametré dans service.yaml
            try{
                $imageFile->move($this->getParameter('photos_categories'), $fileName);
            }catch(FileException $e){
                //gestion des erreurs upload image
            }
            $categorie->setPhoto($fileName);
            //on récupère le manager de doctrine
            
           // $categorie->setDateEnregistrement(new DateTime("now"));
            // $manager = $this->getDoctrine()->getManager();
            
            $manager->persist($categorie);
            $manager->flush();
            $this->addFlash("success", "felicitations, votre categorie à bien été rajoutée!");
            
        }
           
        return $this->render("admin/formCategorie.html.twig", [
            "formCategorie" => $form->createView()
        ]);
    }

    //*************ROUTE EDIT & UPDATE CATEGORIE PAR ID ***********/
    #[Route("/update-categorie-{id<\d+>}", name:"update_categorie")]
    public function updateCat($id, CategorieRepository $repo, Request $request,  SluggerInterface $slugger)
    {
         //code pour vérifier l'autentification, si l'user n'est pas connecté
     if( !$this->isGranted('IS_AUTHENTICATED_FULLY')){
         $this->addFlash('error', "Veuillez vous connecter pour acceder à cette page!");
         return $this->redirectToRoute('app_login');
        }
        // si l'utilisateur est connecté mais n'est pas admin
    if(!$this->isGranted("ROLE_ADMIN")){
        $this->addFlash('error', "Vous êtes un intrus, vous ne pouvez pas vous connecter à cette page!!!");
        return $this->redirectToRoute('app_home');
    }
        $categorie = $repo->find($id);
        
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid())
        {
             $imageFile=$form->get('photoForm')->getData();
            
            //on cree un nouveu nom pour la nouvelle image
            $fileName = $slugger->slug($categorie->getNom()) . uniqid() . '.' . $imageFile->guessExtension();
            
            //on deplace l'image dans le dossier parametré dans service.yaml
            try{
                $imageFile->move($this->getParameter('photos_categories'), $fileName);
            }catch(FileException $e){
                //gestion des erreurs upload image
            }
            $categorie->setPhoto($fileName);
            //on récupère le manager de doctrine
            
            $repo->add($categorie, 1);
            $this->addFlash("success", "felicitations, votre categorie à bien été modifiée!");
            return $this->redirectToRoute("admin_gestion_categories");

        }
          
        return $this->render("admin/formCategorie.html.twig", [
            "formCategorie" => $form->createView()
        ]);
    }
    //**************ROUTE SHOW CATEGORIE PAR ID = SHOW DETAILS********/
    #[Route('/categorie/{id<\d+>}', name: 'details_categorie')]
    public function showCat($id, CategorieRepository $repo): Response
    {
        $categorie =$repo-> find($id);

        return $this->render("admin/details-categorie.html.twig",[
            'categorie' => $categorie
        ]);
    }

     //**************ROUTE DELETE CATEGORIE PAR ID **************/

        #[Route("/delete-categorie-{id<\d+>}", name:"delete_categorie")]
    
    public function deleteCat($id, CategorieRepository $repo)
    {
         //code pour vérifier l'autentification, si l'user n'est pas connecté
     if( !$this->isGranted('IS_AUTHENTICATED_FULLY')){
         $this->addFlash('error', "Veuillez vous connecter pour acceder à cette page!");
         return $this->redirectToRoute('app_login');
        }
        // si l'utilisateur est connecté mais n'est pas admin
    if(!$this->isGranted("ROLE_ADMIN")){
        $this->addFlash('error', "Vous êtes un intrus, vous ne pouvez pas vous connecter à cette page!!!");
        return $this->redirectToRoute('app_home');
    }
        $categorie = $repo->find($id);
        
        $repo->remove($categorie, 1);
             $this->addFlash("success", "felicitations, votre categorie à bien été suprimée!");
        return $this->redirectToRoute("admin_gestion_categories");
    }

     //*****ROUTE POUR OBTENIR TOUTES LES CATEGORIES************/
    #[Route('/gestion-categories', name: 'gestion_categories')]
    public function gestionCategories(CategorieRepository $repo): Response
    {
         //code pour vérifier l'autentification, si l'user n'est pas connecté
     if( !$this->isGranted('IS_AUTHENTICATED_FULLY')){
         $this->addFlash('error', "Veuillez vous connecter pour acceder à cette page!");
         return $this->redirectToRoute('app_login');
        }
        // si l'utilisateur est connecté mais n'est pas admin
    if(!$this->isGranted("ROLE_ADMIN")){
        $this->addFlash('error', "Vous êtes un intrus, vous ne pouvez pas vous connecter à cette page!!!");
        return $this->redirectToRoute('app_home');
    }
        $categories = $repo->findAll();
  
        return $this-> render("admin/gestion-categories.html.twig",[
            'categories' =>$categories,
        ]);
    }
}
