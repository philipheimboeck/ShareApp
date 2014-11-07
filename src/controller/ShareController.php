<?php
/**
 * User: Philip Heimböck
 * Date: 31.10.14
 * Time: 22:29
 */

namespace src\controller;


use src\model\CollectionService;
use src\model\ShareService;
use src\model\UserService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Twig_Environment;

class ShareController
{

    protected $twig;
    protected $share_service;
    protected $user_service;
    protected $collection_service;

    function __construct(ShareService $share_service, UserService $user_service, CollectionService $collection_service, Twig_Environment $twig)
    {
        $this->share_service = $share_service;
        $this->user_service = $user_service;
        $this->collection_service = $collection_service;
        $this->twig = $twig;
    }

    public function indexAction(Request $request)
    {
        $email = $request->getSession()->get('login');
        if ($email === null) {
            return new RedirectResponse('login');
        }

        $shares = $this->share_service->getUserShares($email);
        $collections = $this->user_service->getCollections($email);
        $friends = $this->user_service->getFriends($email);

        return $this->twig->render('content.html.twig', array('shares' => $shares, 'collections' => $collections, 'friends' => $friends));
    }

    public function shareAction(Request $request)
    {
        $email = $request->getSession()->get('login');
        if (!$email) {
            return new RedirectResponse('login');
        }

        $content = $request->get('share');

        $collection = array();
        $matches = array();
        if (preg_match_all('/\+\S+/', $content, $matches)) {
            foreach($matches as $match) {
                // Send to other User
                $collection = $this->collection_service->getForeignInboxCollection($email, $match[0]);
            }
        }

        if (preg_match_all('/@\S+/', $content, $matches)) {
            // Send to chosen Collection
            $collection = $this->collection_service->getCollection($email, $matches[0]);
        }

        if ( empty($collection)) {
            $collection[] = $this->collection_service->getOwnInboxCollection($email);
        }

        try {
            $this->share_service->createShare($email, $content, $collection);
        } catch ( \Exception $exception ) {
            $request->getSession()->getFlashBag()->add('error', $exception->getMessage());
        }

        return new RedirectResponse('/shares');
    }
}