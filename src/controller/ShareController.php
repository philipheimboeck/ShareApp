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
        $login = $request->getSession()->get('login');
        if ($login === null) {
            return new RedirectResponse('login');
        }

        $shares = $this->share_service->getUserShares($login['id']);
        $collections = $this->user_service->getCollections($login['id']);
        $friends = $this->user_service->getFriends($login['id']);

        return $this->twig->render('content.html.twig', array('shares' => $shares, 'collections' => $collections, 'friends' => $friends));
    }

    public function shareAction(Request $request)
    {
        $login = $request->getSession()->get('login');
        if (!$login) {
            return new RedirectResponse('login');
        }

        $content = $request->get('share');

        // Find links
        $link_pattern = '/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/';
        $content = preg_replace($link_pattern, '<a href="$0">$0</a>', $content);

        try {
            $collection = array();
            $matches = array();
            if (preg_match_all('/\+\S+/', $content, $matches)) {
                foreach ($matches as $match) {
                    // Send to other User
                    $user = ltrim($match[0], ' +');
                    $collection = $this->collection_service->getForeignInboxCollection($login['id'], $user);
                }
            }

            if (preg_match_all('/@\S+/', $content, $matches)) {
                // Send to chosen Collection
                $collection = $this->collection_service->getCollection($login['id'], $matches[0]);
            }

            if (empty($collection)) {
                $collection[] = $this->collection_service->getOwnInboxCollection($login['id']);
            }


            $this->share_service->createShare($login['id'], $content, $collection);

        } catch (\Exception $exception) {
            $request->getSession()->getFlashBag()->add('error', $exception->getMessage());
        }

        return new RedirectResponse('/');
    }
}