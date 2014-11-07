<?php
/**
 * User: Philip Heimböck
 * Date: 06.11.14
 * Time: 18:41
 */

namespace src\model;


use Exception;
use src\repository\ShareRepository;
use src\repository\UserRepository;

class ShareService
{
    protected $share_repository;
    protected $user_repository;

    function __construct(ShareRepository $share_repository, UserRepository $user_repository)
    {
        $this->share_repository = $share_repository;
        $this->user_repository = $user_repository;
    }

    public function getUserShares($email)
    {
        return $this->share_repository->getUserShares($email);
    }

    public function createShare($email, $content, $collection)
    {
        // Validate

        // Existing user?
        if ( empty($this->user_repository->getUser($email))) {
            throw new Exception('error.user.notexisting');
        }

        // Content Length
        if ( strlen(($content) > 2048)) {
            throw new Exception('error.maxsize.exceeded');
        }

        // Share the url
        return $this->share_repository->createShare($email, $content, $collection);
    }

} 