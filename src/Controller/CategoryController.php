<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use Symfony\Component\Security\Http\Attribute\IsGranted;

class CategoryController extends AbstractController
{
    #[Route("/page-test", name:"test")]
    public function test(): Response
    {
        return $this->render('categorie/test.html.twig');
    }
    #[Route('/categories', name: 'app_categories')]
    public function index(): Response
    {
        return $this->render('category/index.html.twig', [
            'controller_name' => 'CategoryController',
        ]);
    }

    #[Route('/category/create', name: 'app_create_category')]
    #[IsGranted('ROLE_ADMIN')]
    public function create(): Response
    {
        // 2 in 1 route: view category creation form and handle category creation request
        return $this->render('product/index.html.twig', [
            'controller_name' => 'CategoryController',
        ]);
    }

    #[Route('/category/delete/{id}', name: 'app_delete_category')]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(): Response
    {
        // 2 in 1 route: view category deletion form and handle category creation request
        return $this->render('category/index.html.twig', [
            'controller_name' => 'CategoryController',
        ]);
    }
}
