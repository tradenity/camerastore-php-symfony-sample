<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Tradenity\SDK\Resources\ShoppingCart;
use Tradenity\SDK\Resources\Brand;
use Tradenity\SDK\Resources\Category;
use Tradenity\SDK\Resources\Product;
use Tradenity\SDK\Resources\Collection;

class StoreController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $categories = Category::findAll();
        $collections = Collection::findAll();
        $cart = ShoppingCart::get();
        return $this->render('store/index.html.twig', [
            'cart' => $cart, "categories" => $categories, 'collections' => $collections
        ]);
    }

    /**
     * @Route("/products", name="browse_all")
     */
    public function browseAllAction(Request $request)
    {
        if($request->query->has('query')){
            $products = Product::findAll(['title' => $request->query->get('query')]);
        }else{
            $products = Product::findAll();
        }
        $brands = Brand::findAll();
        $categories = Category::findAll();
        $featured = Collection::findOneBy(['name' => 'featured']);
        $cart = ShoppingCart::get();
        return $this->render('store/products.html.twig', [
            'cart' => $cart, "categories" => $categories, 'brands' => $brands, 'featured' => $featured, 'products' => $products
        ]);
    }

    /**
     * @Route("/categories/{id}", name="browse_category")
     */
    public function browseCategoryAction($id)
    {
        $brands = Brand::findAll();
        $categories = Category::findAll();
        $products = Product::findAllBy(['categories__contains' => $id]);
        $featured = Collection::findOneBy(['name' => 'featured']);
        $cart = ShoppingCart::get();
        return $this->render('store/products.html.twig', [
            'cart' => $cart, "categories" => $categories, 'brands' => $brands, 'featured' => $featured, 'products' => $products
        ]);
    }

    /**
     * @Route("/brands/{id}", name="browse_brand")
     */
    public function browseBrandAction($id)
    {
        $brands = Brand::findAll();
        $categories = Category::findAll();
        $products = Product::findAllBy(["brand" => $id]);
        $featured = Collection::findOneBy(['name' => 'featured']);
        $cart = ShoppingCart::get();
        return $this->render('store/products.html.twig', [
            'cart' => $cart, "categories" => $categories, 'brands' => $brands, 'featured' => $featured, 'products' => $products
        ]);
    }

    /**
     * @Route("/products/{id}", name="show_product")
     */
    public function showProductAction($id)
    {
        $product = Product::findById($id);
        if (!$product) {
            throw $this->createNotFoundException('The product does not exist');
        }
        else {
            $brands = Brand::findAll();
            $categories = Category::findAll();
            $featured = Collection::findOneBy(['name' => 'deals']);
            $cart = ShoppingCart::get();
            // replace this example code with whatever you need
            return $this->render('store/single.html.twig', [
                'cart' => $cart, "categories" => $categories, 'brands' => $brands, 'featured' => $featured, 'product' => $product
            ]);
        }
    }


}
