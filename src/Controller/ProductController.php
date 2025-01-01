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

class ProductController extends AbstractController
{
    #[Route('/products', name: 'homepage')]
    public function index(EntityManagerInterface $em): Response
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

        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('upload_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', "Could not add selected image");
                    return $this->redirectToRoute('app_product_add');
                }

                $product->setImage($newFilename);
            }

            $em->persist($product);
            $em->flush();
            $this->addFlash('success', 'Successfully added product');
            return $this->redirectToRoute('homepage');
        }

        $products = $em->getRepository(Product::class)->findAll();

        return $this->render('product/add.html.twig', [
            'products' => $products,
            'add_product' => $form
        ]);
    }

    #[Route('/product/delete/{id}', name: 'app_product_delete')]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, EntityManagerInterface $em, Product $product = null): Response
    {
        // 2 in 1 route: view product deletion form and handle product creation request
        if($product == null){
            $this->addFlash('error', 'Could not find product');
            return $this->redirectToRoute('app_product');
        }

        if ($this->isCsrfTokenValid('delete' . $product->getId(), $request->request->get('csrf'))) {
            $em->remove($product);
            $em->flush();
            
            $this->addFlash('success', 'Successfully deleted product');
        }
        return $this->redirectToRoute('homepage');   
    }

    #[Route('/product/{id}', name: 'app_product_view')]
    public function view(Product $product): Response
    {
        return $this->render('product/single.html.twig', [
            'product' => $product,
        ]);
    }
}
