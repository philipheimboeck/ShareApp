<?php
/**
 * User: Philip Heimböck
 * Date: 31.10.14
 * Time: 22:29
 */

namespace src\controller;


use src\model\UserService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class LoginController {

    protected $user_service;
    protected $twig;

    function __construct(UserService $user_service, \Twig_Environment $twig)
    {
        $this->user_service = $user_service;
        $this->twig = $twig;
    }


    public function indexAction() {
        return $this->twig->render('login.html.twig');
    }

    public function loginAction(Request $request) {
        $email = $request->get('email');
        $password = $request->get('password');

        if ($this->user_service->checkLogin($email, $password)) {
            $request->getSession()->set('login', true);
            return new Response("Logged in successfully");
        }
        return new RedirectResponse('login');
    }

    public function logoutAction(Request $request) {
        $request->getSession()->clear();
        return new RedirectResponse('login');
    }

} 