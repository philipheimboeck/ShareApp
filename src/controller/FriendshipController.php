<?php
/**
 * User: Philip Heimböck
 * Date: 20.11.14
 * Time: 14:34
 */

namespace src\controller;


use src\model\FriendshipService;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class FriendshipController
{
    protected $friendship_service;
    protected $twig;

    function __construct(FriendshipService $friendship_service, \Twig_Environment $twig)
    {
        $this->friendship_service = $friendship_service;
        $this->twig = $twig;
    }

    public function indexAction(Request $request)
    {
        $login = $request->getSession()->get('login');
        if ($login === null) {
            return new RedirectResponse('login');
        }

        $requests = $this->friendship_service->getRequests($login['id']);

        return $this->twig->render('requests.html.twig', array('requests' => $requests));
    }

    public function submitAction(Request $request)
    {
        $login = $request->getSession()->get('login');
        if ($login === null) {
            return new RedirectResponse('login');
        }

        $friend = $request->get('friend');

        try {
            if ($this->friendship_service->sendRequest($login['id'], $friend)) {
                $request->getSession()->getFlashBag()->add('notice', 'request.send');
            } else {
                $request->getSession()->getFlashBag()->add('error', 'request.failed');
            }
        } catch (Exception $ex) {
            $request->getSession()->getFlashBag()->add('error', $ex->getMessage());
        }

        return $this->twig->render('requests.html.twig');
    }

    public function answerAction($id, Request $request)
    {
        $login = $request->getSession()->get('login');
        if ($login === null) {
            return new RedirectResponse('login');
        }

        $accepted = $request->get('acceptance') === 'true';

        try {
            if ($this->friendship_service->answerRequest($login['id'], $id, $accepted)) {
                $request->getSession()->getFlashBag()->add('notice', 'request.accepted');
            } else {
                $request->getSession()->getFlashBag()->add('error', 'request.failed');
            }
        } catch (Exception $ex) {
            $request->getSession()->getFlashBag()->add('error', $ex->getMessage());
        }


        return new RedirectResponse('/friends');
    }
} 