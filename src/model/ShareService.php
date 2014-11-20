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

    public function getUserShares($user_id)
    {
        return $this->share_repository->getUserShares($user_id);
    }

    public function createShare($user_id, $content, array $collections)
    {
        // Validate

        // Existing user?
        if ( empty($this->user_repository->isUserExistingById($user_id))) {
            throw new Exception('error.user.notexisting');
        }

        // Content Length
        if ( strlen(($content) > 2048)) {
            throw new Exception('error.maxsize.exceeded');
        }

        // Share the url
        foreach ( $collections as $collection) {
            $this->share_repository->createShare($user_id, $content, $collection);
        }
    }

} 