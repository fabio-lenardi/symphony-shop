<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

use App\Entity\User;
use App\Entity\Basket;
use App\Entity\BasketContent;
use App\Repository\ProductRepository;
use App\Repository\BasketRepository;

use Symfony\Component\Security\Http\Attribute\IsGranted;    // use role checking feature
use App\Entity\Product;                                     // use product entity
use App\Form\ProductType;                                   // use product publishing form
use Symfony\Component\HttpFoundation\Request;               // use form submission feature
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class BasketController extends AbstractController
{
    #[Route('/basket', name: 'app_basket_current')]
    public function index(EntityManagerInterface $em): Response
    {
        /** @var User $user */ // Type hint the $user variable
        $user = $this->getUser(); // Get the currently logged-in user

        if (!$user) {
            throw $this->createAccessDeniedException('User not logged in.');
        }

        // view current user basket
        $basket = $em->getRepository(Basket::class)->findOneBy(['user' => $user, 'purchased' => false,]);

        return $this->render('basket/index.html.twig', [
            'basket' => $basket,
        ]);
    }

    #[Route('/history', name: 'app_basket_history')]
    public function history(): Response
    {
        // view user past and paid baskets
        return $this->render('basket/index.html.twig', [
            'controller_name' => 'BasketController',
        ]);
    }

    #[Route('/basket/{id}', name: 'app_basket_old')]
    public function old(): Response
    {
        // view past basket details
        return $this->render('basket/index.html.twig', [
            'controller_name' => 'BasketController',
        ]);
    }

    #[Route('/basket/add/{id}', name: 'app_basket_add', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function add(
        Request $request,
        Product $product,
        Basket $basket,
        EntityManagerInterface $em
    ): Response {
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException('User not logged in.');
        }

        $basket = $em->getRepository(Basket::class)->findOneBy(['user' => $user, 'purchased' => false,]);

        if (!$basket) {
            $basket = new Basket();
            $basket->setUser($user);
            $em->persist($basket);
        }

        // Check if the product is already in the basket
        $basketItem = $basket->getBasketContents()->filter(function (BasketContent $item) use ($product) {
            return $item->getProduct() === $product;
        })->first();

        if ($basketItem) {
            // Product already in basket, increment quantity
            $quantity = $basketItem->getQuantity();
            $basketItem->setQuantity($quantity + 1);
        } else {
            // Product not in basket, create a new BasketItem
            $basketItem = new BasketContent();
            $basketItem->setBasket($basket);
            $basketItem->setProduct($product);
            $basketItem->setQuantity(1);
            $em->persist($basketItem);
        }

        $em->flush();

        $this->addFlash('success', 'Product added to basket!');

        // Redirect back to the product page or wherever appropriate
        return $this->redirect($request->headers->get('referer') ?? $this->generateUrl('app_products')); // Redirect back or to products
    }
}
