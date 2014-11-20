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
use Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;

class LoginController
{
    const EMAIL_PATTERN = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/';

    protected $user_service;
    protected $twig;

    function __construct(UserService $user_service, \Twig_Environment $twig)
    {
        $this->user_service = $user_service;
        $this->twig = $twig;
    }

    public function indexAction()
    {
        return $this->twig->render('login.html.twig');
    }

    public function loginAction(Request $request)
    {
        if ($request->get('submit') === 'login') {

            $user = $request->get('user');
            $password = $request->get('password');

            $user_array = $this->user_service->checkLogin($user, $password);

            if ($user_array !== false) {
                $request->getSession()->set('login', $user_array);
                return new RedirectResponse('/');
            }
            $request->getSession()->getFlashBag()->add('error', 'login.failed');
            return $this->twig->render('login.html.twig', array('email' => $user_array['email']));

        } else if ($request->get('submit') === 'register') {
            return new RedirectResponse('register');
        }
        return new NotAcceptableHttpException();
    }

    public function registerAction(Request $request)
    {
        return $this->twig->render('register.html.twig');
    }

    public function registerSubmitAction(Request $request)
    {
        $msg = null;
        $email = $request->get('email');
        $username = $request->get('user');
        $password = $request->get('password');
        $password2 = $request->get('password_2');

        if ($password === $password2) {
            $user = $this->user_service->register($email, $username, $password);

            if ($user) {
                $request->getSession()->set('login', $user);
                return new RedirectResponse('/');
            }
            $msg = 'register.email.used';
        } else {
            $msg = 'register.incorrect.password';
        }

        $request->getSession()->getFlashBag()->add('error', $msg);
        return $this->twig->render('register.html.twig', array('email' => $email, 'user' => $username, 'password' => $password));
    }

    public function logoutAction(Request $request)
    {
        $request->getSession()->clear();
        return new RedirectResponse('login');
    }

} 