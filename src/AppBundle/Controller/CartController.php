<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Tradenity\SDK\Entities\Category;
use Tradenity\SDK\Entities\ShoppingCart;

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
        $cart = ShoppingCart::add($request->request->get('product'), (int)$request->request->get('quantity'));
        return new JsonResponse(array('total' => $cart->total, 'count' => $cart->count));
    }

    /**
     * @Route("/cart/remove/{id}", name="remove_from_cart")
     */
    public function removeAction(Request $request, $id)
    {
        $cart = ShoppingCart::removeItem($id);
        return new JsonResponse(array('total' => $cart->total, 'count' => $cart->count));
    }
}
