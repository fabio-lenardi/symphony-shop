<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

use Symfony\Component\Security\Http\Attribute\IsGranted;    // use role checking feature
use App\Entity\Category;                                    // use category entity
use App\Form\CategoryType;                                  // use category creation form
use Symfony\Component\HttpFoundation\Request;               // use form submission feature

class CategoryController extends AbstractController
{
    #[Route('/categories', name: 'app_categories')]
    public function index(): Response
    {
        return $this->render('category/index.html.twig', [
            'controller_name' => 'CategoryController',
        ]);
    }
   
    #[Route("/page-test", name:"test")]
    public function test(): Response
    {
        return $this->render('categorie/test.html.twig');
    }

    #[Route('/category/create', name: 'app_category_create')]
    #[IsGranted('ROLE_ADMIN')]
    public function create(EntityManagerInterface $em, Request $request): Response
    {
        // 2 in 1 route: view category creation form and handle category creation request
        $product = new Category();
        $form = $this->createForm(CategoryType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($product);
            $em->flush();
            $this->addFlash('success', 'Successfully added category');
            return $this->redirectToRoute('app_categories');
        }

        return $this->render('category/add.html.twig', [
            'add_category' => $form
        ]);
    }

    #[Route('/category/delete/{id}', name: 'app_category_delete')]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, EntityManagerInterface $em, Category $category = null): Response
    {
        // 2 in 1 route: view category deletion form and handle category creation request
        if($category == null){
            $this->addFlash('error', 'Could not find category');
            return $this->redirectToRoute('app_product');
        }

        if ($this->isCsrfTokenValid('delete' . $category->getId(), $request->request->get('csrf'))) {
            $em->remove($category);
            $em->flush();
            
            $this->addFlash('success', 'Successfully deleted category');
        }
        return $this->redirectToRoute('app_categories');   
    }
}

