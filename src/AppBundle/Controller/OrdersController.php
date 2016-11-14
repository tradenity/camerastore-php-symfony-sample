<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Tradenity\SDK\Entities\Address;
use Tradenity\SDK\Entities\Order;

class OrdersController extends Controller
{
    /**
     * @Route("/orders", name="list_orders")
     */
    public function indexAction(Request $request)
    {
        $customer = $this->getUser()->customer;
        $orders = Order::findAllByCustomer($customer);
        return $this->render('orders/index.html.twig', [
            'orders' => $orders
        ]);
    }

    /**
     * @Route("/orders/checkout", name="new_order")
     */
    public function newAction(Request $request)
    {
        $stripeKey = $this->container->getParameter('stripe_public_key');
        $order = new Order();
        $order->customer = $this->getUser()->customer;
        $order->billingAddress = $this->createAddress();
        $order->shippingAddress = $this->createAddress();
        return $this->render('orders/checkout.html.twig', [
            'order' => $order, 'stripeKey' => $stripeKey
        ]);
    }

    /**
     * @Route("/orders/create", name="create_order")
     * @Method({"POST"})
     */
    public function createAction(Request $request)
    {
        $input = $request->request;
        $token = $input->get('token');

        $order = new Order();
        $order->customer = $this->getUser()->customer;
        $order->billingAddress = $this->getBillingAddress($input);
        $order->shippingAddress = $this->getShippingAddress($input);

        $transaction = $order->checkout($token);
        $orderId = $transaction->order->id;
        return $this->redirect("/orders/${orderId}");
    }

    /**
     * @Route("/orders/{id}", name="show_order")
     */
    public function showAction(Request $request, $id)
    {
        $order = Order::findById($id);
        return $this->render('orders/show.html.twig', [
            'order' => $order
        ]);
    }

    /**
     * @Route("/orders/refund/{id}", name="refund_order")
     * @Method({"POST"})
     */
    public function refundAction(Request $request, $id)
    {
        $transaction = Order::refund($id);
        $orderId = $transaction->order->id;
        return $this->redirect("/orders/${orderId}");
    }
    
    
    private function getBillingAddress($input)
    {
        $a = new Address();
        $a->streetLine1 = $input->get('billingAddress_streetLine1');
        $a->streetLine2 = $input->get('billingAddress_streetLine2');
        $a->city = $input->get('billingAddress_city');
        $a->state = $input->get('billingAddress_state');
        $a->zipCode = $input->get('billingAddress_zipCode');
        $a->country = $input->get('billingAddress_country');
        return $a;
    }

    private function getShippingAddress($input)
    {
        $a = new Address();
        $a->streetLine1 = $input->get('shippingAddress_streetLine1');
        $a->streetLine2 = $input->get('shippingAddress_streetLine2');
        $a->city = $input->get('shippingAddress_city');
        $a->state = $input->get('shippingAddress_state');
        $a->zipCode = $input->get('shippingAddress_zipCode');
        $a->country = $input->get('shippingAddress_country');
        return $a;
    }
    private function createAddress()
    {
        $a = new Address();
        $a->streetLine1="3290 Hermosillo Place";
        $a->streetLine2="";
        $a->city="Washington";
        $a->state="Washington, DC";
        $a->zipCode="20521-3290";
        $a->country="USA";
        return $a;
    }


}
