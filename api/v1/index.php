<?php
define('_JEXEC', 1);
define('_API', 1);

define('JPATH_BASE', dirname(dirname(dirname(__FILE__))));
define('JPATH_ADMINISTRATOR', dirname(dirname(dirname(__FILE__))) . '/administrator');

// Include the Joomla framework
require_once(JPATH_BASE . '/includes/defines.php');
require_once(JPATH_BASE . '/includes/framework.php');

$application = &JFactory::getApplication('site');
$application->initialise();

require '../Slim/Slim.php';

\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim(array(
	'mode' => 'development'
));

$app->_db = JFactory::getDbo();
$app->view(new \JsonApiView());
$app->add(new \JsonApiMiddleware());

//Main entry
$app->get('/', function () use ($app)
{
	$app->render(200, array(
		'msg' => 'You reach the JAB API V1',
	));
});

// Content
$app->map('/content/', function () use ($app)
{
	$query = $app->_db->getQuery(true);
	$query->select('*')
		->from($app->_db->quoteName('#__content'))
		->where($app->_db->quoteName('state') . ' = ' . $app->_db->quote('1'));
	$app->_db->setQuery($query);

	$app->render(200, array(
		'msg' => $app->_db->loadObjectList(),
	));
})->via('GET');

$app->map('/content/:id', function ($id) use ($app)
{
	$query = $app->_db->getQuery(true);
	$query->select('*')
		->from($app->_db->quoteName('#__content'))
		->where('id = ' . $app->_db->quote($id)
			. ' AND ' . $app->_db->quoteName('state') . ' = ' . $app->_db->quote('1')
		);
	$app->_db->setQuery($query);

	$app->render(200, array(
		'msg' => $app->_db->loadObject(),
	));
})->via('GET');

$app->run();
