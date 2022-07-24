<?php

namespace App\Controller;

use App\Repository\CategorieRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(CategorieRepository $repo): Response
    { 
        $categories = $repo->findAll(); 
        return $this->render('home/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    //*****ROUTE POUR OBTENIR UNE CATEGORIE COTÉ USER************/
    #[Route('/categorie/{id<\d+>}', name: 'categorie_show')]
    public function show($id, CategorieRepository $repo): Response
    {
        $categorie =$repo-> find($id);

        return $this->render("categorie/showCategorie.html.twig",[
            'categorie' => $categorie
        ]);
    }

        #[Route('/cgu', name: 'home_cgu')]
    public function cgv(): Response
    {
        return $this->render('static/cgu.html.twig');
    }

        #[Route('/qui', name: 'home_qui')]
    public function qui(): Response
    {
        return $this->render('static/quiSommes.html.twig');
    }

        #[Route('/contact', name: 'home_contact')]
    public function contact(): Response
    {
        return $this->render('static/contact.html.twig');
    }

        #[Route('/liens', name: 'home_liens')]
    public function liens(): Response
    {
        return $this->render('static/liens.html.twig');
    }

}
