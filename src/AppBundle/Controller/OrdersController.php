<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Tradenity\SDK\Resources\PageRequest;
use Tradenity\SDK\Resources\Address;
use Tradenity\SDK\Resources\Country;
use Tradenity\SDK\Resources\State;
use Tradenity\SDK\Resources\Customer;
use Tradenity\SDK\Resources\Order;
use Tradenity\SDK\Resources\ShoppingCart;
use Tradenity\SDK\Resources\ShippingMethod;
use Tradenity\SDK\Resources\PaymentToken;
use Tradenity\SDK\Resources\CreditCardPayment;

class OrdersController extends Controller
{
    /**
     * @Route("/orders", name="list_orders")
     */
    public function indexAction(Request $request)
    {
        $customer = $this->getUser()->getCustomer();
        $orders = Order::findAllBy(['customer' => $customer->getId()]);
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
        $usa = Country::findOneBy(['iso2' => "US"]);
        $countries = Country::findAll(new PageRequest(0, 250));
        $states = State::findAllBy(['country' => $usa->getId(), 'size' => 60, 'sort' => "name"]);
        $cart = ShoppingCart::get();
        $order = new Order();
        $order->customer = $this->getUser()->getCustomer();
        $order->billingAddress = $this->createAddress($usa);
        $order->shippingAddress = $this->createAddress($usa);
        return $this->render('orders/checkout.html.twig', [
            'order' => $order, 'stripeKey' => $stripeKey, 'cart' => $cart, 'countries' => $countries, 'states' => $states
        ]);
    }

    /**
     * @Route("/orders/create", name="create_order")
     * @Method({"POST"})
     */
    public function createAction(Request $request)
    {
        $input = $request->request;

        $order = new Order([
            'customer' => $this->getUser()->getCustomer(),
            'billingAddress' => $this->getBillingAddress($input),
            'shippingAddress' => $this->getShippingAddress($input)
        ]);
        $order->create();
        $request->getSession()->set('orderId', $order->getId());
        $shippingMethods = ShippingMethod::findAllForOrder($order->getId());
        return $this->render('orders/shipping_form.html.twig', [
            'shippingMethods' => $shippingMethods
        ]);
    }

    /**
     * @Route("/orders/shipping", name="add_shipping")
     * @Method({"POST"})
     */
    public function addShippingAction(Request $request)
    {
        $input = $request->request;
        $order = Order::findById($request->getSession()->get('orderId'));
        $order->setShippingMethod(new ShippingMethod(['id' => $input->get('shipping_method')]));
        $order->update();
        return $this->render('orders/payment_form.html.twig', []);
    }

    /**
     * @Route("/orders/payment", name="add_payment")
     * @Method({"POST"})
     */
    public function addPaymentAction(Request $request)
    {
        $input = $request->request;
        $order = Order::findById($request->getSession()->get('orderId'));
        $paymentSource = new PaymentToken(['token' => $input->get('token'), 'customer' => $this->getUser()->getCustomer(), 'status' => "new"]);
        $paymentSource->create();
        $cardPayment = new CreditCardPayment(['order' => $order, 'paymentSource' => $paymentSource]);
        $cardPayment->create();
        $orderId = $order->getId();
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
        return new Address([
            'streetLine1' => $input->get('billingAddress_streetLine1'),
            'streetLine2' => $input->get('billingAddress_streetLine2'),
            'city' => $input->get('billingAddress_city'),
            'state' => new State(['id' => $input->get('billingAddress_state')]),
            'zipCode' => $input->get('billingAddress_zipCode'),
            'country' => new Country(['id' => $input->get('billingAddress_country')])
        ]);
    }

    private function getShippingAddress($input)
    {
        return new Address([
            'streetLine1' => $input->get('shippingAddress_streetLine1'),
            'streetLine2' => $input->get('shippingAddress_streetLine2'),
            'city' => $input->get('shippingAddress_city'),
            'state' => new State(['id' => $input->get('shippingAddress_state')]),
            'zipCode' => $input->get('shippingAddress_zipCode'),
            'country' => new Country(['id' => $input->get('shippingAddress_country')])
        ]);
    }

    private function createAddress($country)
    {
        $a = new Address();
        $a->setStreetLine1("3290 Hermosillo Place");
        $a->setStreetLine2("");
        $a->setCity("Washington");
        $a->setState(new State());
        $a->setZipCode("20521-3290");
        $a->setCountry($country);
        return $a;
    }


}
