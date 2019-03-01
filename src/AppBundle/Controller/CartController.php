<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Tradenity\SDK\Resources\Category;
use Tradenity\SDK\Resources\ShoppingCart;
use Tradenity\SDK\Resources\LineItem;
use Tradenity\SDK\Resources\Product;

class CartController extends Controller
{
    /**
     * @Route("/cart", name="show_cart")
     */
    public function indexAction(Request $request)
    {
        $cart = ShoppingCart::get();
        $categories = Category::findAll();
        return $this->render('store/cart.html.twig', [
            'categories' => $categories, 'cart' => $cart
        ]);
    }

    /**
     * @Route("/cart/add", name="add_to_cart")
     */
    public function addAction(Request $request)
    {
        $productId = $request->request->get('product');
        $quantity = (int)$request->request->get('quantity');
        $cart = ShoppingCart::addItem(new LineItem(['product' => new Product(['id' => $productId]), 'quantity' => $quantity]));
        return new JsonResponse(array('total' => $cart->getTotal(), 'count' => count($cart->getItems())));
    }

    /**
     * @Route("/cart/remove/{id}", name="remove_from_cart")
     */
    public function removeAction(Request $request, $id)
    {
        $cart = ShoppingCart::deleteItem($id);
        return new JsonResponse(array('total' => $cart->getTotal(), 'count' => count($cart->getItems())));
    }
}
