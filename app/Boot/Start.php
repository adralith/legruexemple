<?php
/**
 * Boot Handler - perform the Application's boot stage.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

use Nova\Config\EnvironmentVariables;
use Nova\Config\Config;
use Nova\Config\Repository as ConfigRepository;
use Nova\Foundation\AliasLoader;
use Nova\Foundation\Application;
use Nova\Http\Request;
use Nova\Support\Facades\Facade;

use Symfony\Component\HttpFoundation\BinaryFileResponse;

//--------------------------------------------------------------------------
// Set PHP Error Reporting Options
//--------------------------------------------------------------------------

error_reporting(-1);

//--------------------------------------------------------------------------
// Use Internally The UTF-8 Encoding
//--------------------------------------------------------------------------

if (function_exists('mb_internal_encoding')) {
    mb_internal_encoding('utf-8');
}

//--------------------------------------------------------------------------
// Include The Compiled Class File
//--------------------------------------------------------------------------

if (file_exists($compiled = __DIR__ .DS .'Compiled.php')) {
    require $compiled;
}

//--------------------------------------------------------------------------
// Setup Patchwork UTF-8 Handling
//--------------------------------------------------------------------------

Patchwork\Utf8\Bootup::initMbstring();

//--------------------------------------------------------------------------
// Set The System Path
//--------------------------------------------------------------------------

define('SYSTEMDIR', ROOTDIR .str_replace('/', DS, 'vendor/nova-framework/system/'));

//--------------------------------------------------------------------------
// Set The Storage Path
//--------------------------------------------------------------------------

define('STORAGE_PATH', APPDIR .'Storage' .DS);

//--------------------------------------------------------------------------
// Set The Framework Version
//--------------------------------------------------------------------------

define('VERSION', Application::VERSION);

//--------------------------------------------------------------------------
// Load The Global Configuration
//--------------------------------------------------------------------------

$path = APPDIR .'Config.php';

if (is_readable($path)) require $path;

//--------------------------------------------------------------------------
// Create New Application
//--------------------------------------------------------------------------

$app = new Application();

//--------------------------------------------------------------------------
// Detect The Application Environment
//--------------------------------------------------------------------------

$env = $app->detectEnvironment(array(
    'local' => array('darkstar'),
));

//--------------------------------------------------------------------------
// Bind Paths
//--------------------------------------------------------------------------

$app->bindInstallPaths(array(
    'base'    => ROOTDIR,
    'app'     => APPDIR,
    'public'  => PUBLICDIR,
    'storage' => STORAGE_PATH,
));

//--------------------------------------------------------------------------
// Bind The Application In The Container
//--------------------------------------------------------------------------

$app->instance('app', $app);

//--------------------------------------------------------------------------
// Check For The Test Environment
//--------------------------------------------------------------------------

if (isset($unitTesting)) {
    $app['env'] = $env = $testEnvironment;
}

//--------------------------------------------------------------------------
// Load The Framework Facades
//--------------------------------------------------------------------------

Facade::clearResolvedInstances();

Facade::setFacadeApplication($app);

//--------------------------------------------------------------------------
// Register Facade Aliases To Full Classes
//--------------------------------------------------------------------------

$app->registerCoreContainerAliases();

//--------------------------------------------------------------------------
// Register The Environment Variables
//--------------------------------------------------------------------------

with($envVariables = new EnvironmentVariables(
    $app->getEnvironmentVariablesLoader()
))->load($env);

//--------------------------------------------------------------------------
// Register The Config Manager
//--------------------------------------------------------------------------

$app->instance('config', $config = new ConfigRepository(
    $app->getConfigLoader(), $env
));

//--------------------------------------------------------------------------
// Register Application Exception Handling
//--------------------------------------------------------------------------

$app->startExceptionHandling();

if ($env != 'testing') ini_set('display_errors', 'Off');

//--------------------------------------------------------------------------
// Set The Default Timezone From Configuration
//--------------------------------------------------------------------------

$config = $app['config']['app'];

date_default_timezone_set($config['timezone']);

//--------------------------------------------------------------------------
// Register The Alias Loader
//--------------------------------------------------------------------------

$aliases = $config['aliases'];

AliasLoader::getInstance($aliases)->register();

//--------------------------------------------------------------------------
// Enable HTTP Method Override
//--------------------------------------------------------------------------

Request::enableHttpMethodParameterOverride();

//--------------------------------------------------------------------------
// Enable Trusting Of X-Sendfile Type Header
//--------------------------------------------------------------------------

BinaryFileResponse::trustXSendfileTypeHeader();

//--------------------------------------------------------------------------
// Register The Core Service Providers
//--------------------------------------------------------------------------

$providers = $config['providers'];

$app->getProviderRepository()->load($app, $providers);

//--------------------------------------------------------------------------
// Additional Middleware On Application
//--------------------------------------------------------------------------

App::middleware('Shared\Http\ContentGuard', array(
    $app['config']['app.debug']
));

//--------------------------------------------------------------------------
// Register Booted Start Files
//--------------------------------------------------------------------------

$app->booted(function() use ($app, $env)
{

//--------------------------------------------------------------------------
// Load The Application Start Script
//--------------------------------------------------------------------------

$path = $app['path'] .DS .'Boot' .DS .'Global.php';

if (is_readable($path)) require $path;

//--------------------------------------------------------------------------
// Load The Environment Start Script
//--------------------------------------------------------------------------

$path = $app['path'] .DS .'Boot' .DS .'Environment' .DS .ucfirst($env) .'.php';

if (is_readable($path)) require $path;

//--------------------------------------------------------------------------
// Load The Application Events
//--------------------------------------------------------------------------

$path = $app['path'] .DS .'Events.php';

if (is_readable($path)) require $path;

//--------------------------------------------------------------------------
// Load The Application's Route Filters
//--------------------------------------------------------------------------

$path = $app['path'] .DS .'Filters.php';

if (is_readable($path)) require $path;

//--------------------------------------------------------------------------
// Load The Application Routes
//--------------------------------------------------------------------------

$path = $app['path'] .DS .'Routes.php';

if (is_readable($path)) require $path;

//--------------------------------------------------------------------------
// Load The Application Bootstrap
//--------------------------------------------------------------------------

$path = $app['path'] .DS .'Bootstrap.php';

if (is_readable($path)) require $path;

});

//--------------------------------------------------------------------------
// Return The Application
//--------------------------------------------------------------------------

return $app;
