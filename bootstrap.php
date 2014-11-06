<?php
/**
 * User: Philip Heimböck
 * Date: 31.10.14
 * Time: 22:23
 */
use src\controller\LoginController;
use src\controller\ShareController;
use src\model\UserService;
use src\repository\UserFacade;
use Symfony\Component\HttpFoundation\Response;
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

/*
 * Repositories
 */

$app['repository.user'] = $app->share(function () use ($app) {
    return new UserFacade($app['db']);
});

/*
 * Models
 */

$app['model.user'] = $app->share(function () use ($app) {
    return new UserService($app['repository.user']);
});

/*
 * Controllers
 */

$app['controller.share_controller'] = $app->share(function () use ($app) {
    return new ShareController();
});

$app['controller.login_controller'] = $app->share(function() use ($app) {
    return new LoginController($app['model.user'], $app['twig']);
});

/*
 * Routes
 */

/* Login */

$app->get('/login', "controller.login_controller:indexAction")->bind('login');

$app->post('/login', "controller.login_controller:loginAction")->bind('login_submit');

$app->get('/logout', "controller.login_controller:logoutAction")->bind('logout');

/* Data */
$app->get('/', function () use ($app) {
    if ($app['session']->get('login') === null) {
        return $app->redirect('login');
    }
    return new Response($app['controller.share_controller']->getData());
});

// Debug mode
$app['debug'] = true;

$app->run();
