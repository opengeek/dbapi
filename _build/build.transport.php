<?php
/**
 * @package dbapi
 * @subpackage build
 */
$mtime = microtime();
$mtime = explode(" ", $mtime);
$mtime = $mtime[1] + $mtime[0];
$tstart = $mtime;
set_time_limit(0);

/* define package */
define('PKG_NAME','dbapi');
define('PKG_NAME_LOWER',strtolower(PKG_NAME));
define('PKG_VERSION','1.0.0');
define('PKG_RELEASE','pl');

/* define sources */
$root = dirname(dirname(__FILE__)) . '/';
$sources= array (
    'root' => $root,
    'build' => $root . '_build/',
    'resolvers' => $root . '_build/resolvers/',
    'source_core' => $root.'core/components/'.PKG_NAME_LOWER,
    'plugins' => $root.'core/components/'.PKG_NAME_LOWER.'/elements/plugins/',
    'docs' => $root . 'core/components/'.PKG_NAME_LOWER.'/docs/',
);
unset($root);

/* instantiate MODx */
require_once $sources['build'].'build.config.php';
require_once MODX_CORE_PATH . 'model/modx/modx.class.php';
$modx= new modX();
$modx->initialize('mgr');
$modx->setLogLevel(xPDO::LOG_LEVEL_INFO);
$modx->setLogTarget(XPDO_CLI_MODE ? 'ECHO' : 'HTML');

/* load builder */
$modx->loadClass('transport.modPackageBuilder', '', false, true);
$builder = new modPackageBuilder($modx);
$builder->createPackage(PKG_NAME, PKG_VERSION, PKG_RELEASE);
$builder->registerNamespace('dbapi', false, true, '{core_path}components/'.PKG_NAME_LOWER.'/');

/* add in file vehicle */
$attributes = array (
    'vehicle_class' => 'xPDOFileVehicle',
);
$c = array (
    'source' => $sources['source_core'],
    'target' => "return MODX_CORE_PATH . 'components/';",
);
$vehicle = $builder->createVehicle($c, $attributes);
$builder->putVehicle($vehicle);
unset ($c, $vehicle, $attributes);

/* add in plugin vehicle */
$c= $modx->newObject('modPlugin');
$c->set('name', 'DBAPI Service');
$c->set('description', "<strong>" . PKG_VERSION . "</strong> Plugin to load the Legacy DBAPI class as a service.");
$c->set('category', 0);
$c->set('plugincode', file_get_contents($sources['plugins'] . 'plugin.dbapi.php'));
$attributes= array(
    xPDOTransport::UNIQUE_KEY => 'name',
    xPDOTransport::PRESERVE_KEYS => true,
    xPDOTransport::UPDATE_OBJECT => true,
);
$vehicle = $builder->createVehicle($c, $attributes);
$vehicle->resolve('php',array(
    'source' => $sources['resolvers'].'resolve.plugin_events.php',
));
$builder->putVehicle($vehicle);
unset($vehicle,$attributes,$c);

/* pack in the license file, readme and setup options */
$builder->setPackageAttributes(array(
    'license' => file_get_contents($sources['docs'] . 'license.txt'),
    'readme' => file_get_contents($sources['docs'] . 'readme.txt'),
    'changelog' => file_get_contents($sources['docs'] . 'changelog.txt'),
    'setup-options' => array(
        'source' => $sources['build'].'setup.options.php',
    ),
));

$builder->pack();

$tend= $modx->getMicroTime();
$totalTime= ($tend - $tstart);
$totalTime= sprintf("%2.4f s", $totalTime);

echo "\nExecution time: {$totalTime}\n";

exit ();
