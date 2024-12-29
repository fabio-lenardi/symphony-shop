<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategorieType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;

use Symfony\Component\Security\Http\Attribute\IsGranted;

class CategorieController extends AbstractController
{
    #[Route('/prenom/{prenom}', name: 'homepage')]
    public function index(string $prenom): Response
    {
        return $this->render('categorie/index.html.twig', [
            'prenom' => $prenom,
            'now' => new \DateTime()
        ]);
    }

    #[Route("/page-test", name:"test")]
    public function test(): Response
    {
        return $this->render('categorie/test.html.twig');
    }

    #[Route('/categories', name:'category_list')]
    public function categories(EntityManagerInterface $em, Request $request, TranslatorInterface $translator): Response
    {
        $category = new Category();
        $form = $this->createForm(CategorieType::class, $category);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em->persist($category);
            $em->flush();

            $this->addFlash('success', $translator->trans('category.added'));

            return $this->redirectToRoute('category_list');
        }

        $categories = $em->getRepository(Category::class)->findAll();

        return $this->render(
            'categorie/list.html.twig',
            [
            'categories' => $categories,
            'ajout_category' => $form 
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/category/{id}', name: 'app_category_show')]
    public function show(Category $category = null): Response
    {
        if($category == null){
            $this->addFlash('error', 'Categorie introuvable');
            return $this->redirectToRoute('category_list');
        }

        return $this->render('categorie/show.html.twig', [
            'category' => $category,
        ]);
    }

    #[Route('/category/delete/{id}', name: 'app_category_delete')]
    public function delete(Request $request, EntityManagerInterface $em, Category $category = null)
    {
        if($category == null){
            $this->addFlash('error', 'Categorie introuvable');
            return $this->redirectToRoute('category_list');
        }

        if ($this->isCsrfTokenValid('delete' . $category->getId(), $request->request->get('csrf'))) {
            $em->remove($category);
            $em->flush();

            $this->addFlash('success', 'Categorie supprimée');
        }
        return $this->redirectToRoute('category_list');
    }
}
