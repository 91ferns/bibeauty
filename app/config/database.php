<?php

$db = parse_url(getenv('DATABASE_URL'));

$container->setParameter('database_driver', $db['scheme']);
$container->setParameter('database_user', $db['user']);
$container->setParameter('database_password', $db['pass']);
$container->setParameter('database_host', $db['host']);

if (isset($db['port'])) {
	$container->setParameter('database_port', $db['port']);
}

$container->setParameter('database_name', substr($db['path'], 1));