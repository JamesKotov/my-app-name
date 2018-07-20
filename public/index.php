<?php
if (PHP_SAPI == 'cli-server')
{
	// To help the built-in PHP dev server, check if the request was actually for
	// something which should probably be served as a static file
	$url = parse_url($_SERVER['REQUEST_URI']);
	$file = __DIR__ . $url['path'];
	if (is_file($file))
	{
		return false;
	}
}

require_once __DIR__ . '/../vendor/autoload.php';

// setup Propel
require_once __DIR__ . '/../storage-api/generated-conf/config.php';

// checking DB integrity and creating minimal structures
$aFolders = FolderQuery::create()->findByParentId(-1);

if (sizeof($aFolders) > 1)
{
	die('Invalid DB structure');
}
if (sizeof($aFolders) === 0)
{
	$oFolder = new Folder();
	$oFolder->setParentId(-1);
	$oFolder->setName('root');
	$oFolder->save();
}

session_start();

// Instantiate the app
$settings = require __DIR__ . '/../src/settings.php';
$app = new \Slim\App($settings);

// Set up dependencies
require __DIR__ . '/../src/dependencies.php';

// Register routes
require __DIR__ . '/../src/routes.php';

// Run app
$app->run();
