<?php
/**
 * User: Philip Heimböck
 * Date: 31.10.14
 * Time: 22:23
 */
use src\controller\FriendshipController;
use src\controller\LoginController;
use src\controller\ShareController;
use src\model\CollectionService;
use src\model\FriendshipService;
use src\model\ShareService;
use src\model\UserService;
use src\repository\CollectionRepository;
use src\repository\FriendshipRepository;
use src\repository\ShareRepository;
use src\repository\UserRepository;
use Symfony\Component\Translation\Loader\YamlFileLoader;
use Symfony\Component\Translation\Translator;

require_once __DIR__ . '/vendor/autoload.php';

/*
 * Silex
 */
$app = new Silex\Application();

$app->register(new Silex\Provider\ServiceControllerServiceProvider());

$app->register(new Silex\Provider\SessionServiceProvider());

$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => array(
        'driver' => 'pdo_sqlite',
        'path' => __DIR__ . '/data.db',
    ),
));

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__ . '/src/views',
));
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());

$app->register(new Silex\Provider\TranslationServiceProvider(), array(
    'locale_fallbacks' => array('en'),
));

$app['translator'] = $app->share($app->extend('translator', function (Translator $translator) {
    $translator->addLoader('yaml', new YamlFileLoader());

    $translator->addResource('yaml', __DIR__ . '/src/locales/en.yml', 'en');
    $translator->addResource('yaml', __DIR__ . '/src/locales/de.yml', 'de');

    return $translator;
}));

$app['twig'] = $app->share($app->extend('twig', function($twig) {
    $twig->addFunction(new \Twig_SimpleFunction('asset', function ($asset) {
        // implement whatever logic you need to determine the asset path

        return sprintf('/%s', ltrim($asset, '/'));
    }));

    return $twig;
}));

/*
 * Repositories
 */

$app['repository.user'] = $app->share(function () use ($app) {
    return new UserRepository($app['db']);
});

$app['repository.share'] = $app->share(function () use ($app) {
    return new ShareRepository($app['db']);
});

$app['repository.collection'] = $app->share(function () use ($app) {
    return new CollectionRepository($app['db']);
});

$app['repository.friendship'] = $app->share(function () use ($app) {
    return new FriendshipRepository($app['db']);
});

/*
 * Models
 */

$app['model.user'] = $app->share(function () use ($app) {
    return new UserService($app['repository.user']);
});

$app['model.share'] = $app->share(function () use ($app) {
    return new ShareService($app['repository.share'], $app['repository.user']);
});

$app['model.collection'] = $app->share(function () use ($app) {
    return new CollectionService($app['repository.collection'], $app['repository.user']);
});

$app['model.friendship'] = $app->share(function () use ($app) {
    return new FriendshipService($app['repository.friendship'], $app['repository.user']);
});

/*
 * Controllers
 */

$app['controller.share_controller'] = $app->share(function () use ($app) {
    return new ShareController($app['model.share'], $app['model.user'], $app['model.collection'], $app['twig']);
});

$app['controller.login_controller'] = $app->share(function() use ($app) {
    return new LoginController($app['model.user'], $app['twig']);
});

$app['controller.friendship_controller'] = $app->share(function() use ($app) {
    return new FriendshipController($app['model.friendship'], $app['twig']);
});

/*
 * Routes
 */

/* Login */

$app->get('/login', "controller.login_controller:indexAction")->bind('login');

$app->post('/login', "controller.login_controller:loginAction")->bind('login_submit');

$app->get('/logout', "controller.login_controller:logoutAction")->bind('logout');

$app->get('/register', "controller.login_controller:registerAction")->bind('register');

$app->post('/register', "controller.login_controller:registerSubmitAction")->bind('register_submit');

/* Data */

$app->get('/', "controller.share_controller:indexAction")->bind('shares');

$app->post('/submit', "controller.share_controller:shareAction")->bind('shares_submit');

$app->get('/friends', "controller.friendship_controller:indexAction")->bind('friends');

$app->post('/request', "controller.friendship_controller:submitAction")->bind('request_submit');

$app->post('/request/{id}', "controller.friendship_controller:answerAction")->bind('request_answer');

// Debug mode
$app['debug'] = true;

$app->run();
