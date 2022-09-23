<?php

namespace App\Controller;

use App\Repository\CategorieRepository;
use Symfony\Component\HttpFoundation\Request;
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

      //*****ROUTES vers STATIC COTÉ USER************/
        #[Route('/cgu', name: 'layout_cgu')]
    public function cgu(): Response
    {
        return $this->render('static/cgu.html.twig');
    }

        #[Route('/qui', name: 'layout_qui')]
    public function qui(): Response
    {
        return $this->render('static/quiSommes.html.twig');
    }

        #[Route('/contact', name: 'layout_contact')]
    public function contact(): Response
    {
        return $this->render('static/contact.html.twig');
    }

        #[Route('/liens', name: 'layout_liens')]
    public function liens(): Response
    {
        return $this->render('static/liens.html.twig');
    }

            #[Route('/vieprive', name: 'layout_vieprive')]
    public function vieprive(): Response
    {
        return $this->render('static/vieprive.html.twig');
    }

        #[Route('/remerciements', name: 'layout_remerciements')]
    public function remerciements(): Response
    {
        return $this->render('static/remerciements.html.twig');
    }
   
    //*****ROUTE POUR ACTIVER LANGUE LOCALE DANS LA NAV********/
  
    #[Route('/change-locale/{locale}', name:'change_locale')]
    public function changeLocale($locale, Request $request): Response 
{ 
    // On stocke la langue demandée dans la session
     $request->getSession()->set('_locale', $locale); 
     // On revient sur la page précédente 
     return $this->redirect($request->headers->get('referer')); }

    }


