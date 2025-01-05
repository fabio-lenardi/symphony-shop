<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

use Symfony\Component\Security\Http\Attribute\IsGranted;    // use role checking feature
use App\Entity\Product;                                     // use product entity
use App\Form\ProductType;                                   // use product publishing form
use Symfony\Component\HttpFoundation\Request;               // use form submission feature
use Symfony\Component\HttpFoundation\File\Exception\FileException;

use App\Repository\ProductRepository;

class ProductController extends AbstractController
{
    #[Route('/products', name: 'homepage')]
    
    public function list(ProductRepository $productRepository): Response
    {
        // Récupérer tous les produits depuis la base de données
        $products = $productRepository->findAll();

        // Passer les produits au template Twig
        return $this->render('index.html.twig', [
            'products' => $products, // Notez la clé 'product'
        ]);
    }

    public function index(): Response
    {
        $products = $em->getRepository(Product::class)->findAll();

        return $this->render('index.html.twig', [
            'products' => $products,
        ]);
    }

    #[Route('/product/add', name: 'app_product_add')]
    #[IsGranted('ROLE_ADMIN')]
    public function add(EntityManagerInterface $em, Request $request): Response
    {
        // 2 in 1 route: view product creation form and handle product creation request
        return $this->render('product/index.html.twig', [
            'controller_name' => 'ProductController',
        ]);
    }

    #[Route('/product/delete/{id}', name: 'app_product_delete')]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, EntityManagerInterface $em, Product $product = null): Response
    {
        // 2 in 1 route: view product deletion form and handle product creation request
        return $this->render('product/index.html.twig', [
            'controller_name' => 'ProductController',
        ]);
    }

    #[Route('/product/{id}', name: 'app_product_view')]
    public function view(Product $product): Response
    {
        return $this->render('product/index.html.twig', [
            'controller_name' => 'ProductController',
        ]);
    }
}
