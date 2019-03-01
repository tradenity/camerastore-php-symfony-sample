<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Tradenity\SDK\Resources\Customer;

class AccountsController extends Controller
{
    /**
     * @Route("/register", name="new_account")
     */
    public function newAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('accounts/register.html.twig', [
            'customer' => new Customer()
        ]);
    }

    /**
     * @Route("/accounts/create", name="create_account")
     * @Method({"POST"})
     */
    public function createAction(Request $request)
    {
        $customer = new Customer([
            'firstName' => $request->request->get("firstName"),
            'lastName' => $request->request->get("lastName"),
            'email' => $request->request->get("email"),
            'username' => $request->request->get("username"),
            'password' => $request->request->get("password")
        ]);
        $confirmPassword = $request->request->get("confirmPassword");
        if ($confirmPassword === $customer->getPassword() ) {
            $customer->create();
            return $this->redirect('/login');
        }else{
            return $this->render('accounts/register.html.twig', [
                'customer' => $customer
            ]);
        }
    }
}
