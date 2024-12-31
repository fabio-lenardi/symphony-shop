<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;

use Symfony\Component\Security\Http\Attribute\IsGranted;

class ProductController extends AbstractController
{
    #[Route('/products', name: 'homepage')]
    public function index(): Response
    {
        return $this->render('index.html.twig', [
            'controller_name' => 'ProductController',
        ]);
    }

    #[Route('/product/create', name: 'app_create_product')]
    #[IsGranted('ROLE_ADMIN')]
    public function create(): Response
    {
        // 2 in 1 route: view product creation form and handle product creation request
        return $this->render('product/index.html.twig', [
            'controller_name' => 'ProductController',
        ]);
    }

    #[Route('/product/delete/{id}', name: 'app_delete_product')]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(): Response
    {
        // 2 in 1 route: view product deletion form and handle product creation request
        return $this->render('product/index.html.twig', [
            'controller_name' => 'ProductController',
        ]);
    }

    #[Route('/product/{id}', name: 'app_view_product')]
    public function view(): Response
    {
        return $this->render('product/index.html.twig', [
            'controller_name' => 'ProductController',
        ]);
    }


}
