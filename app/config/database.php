<?php

$db = parse_url(getenv('DATABASE_URL'));

if ($db) {
	$container->setParameter('database_driver', $db['scheme']);
	$container->setParameter('database_user', $db['user']);
	$container->setParameter('database_password', $db['pass']);
	$container->setParameter('database_host', $db['host']);

	if (isset($db['port'])) {
		$container->setParameter('database_port', $db['port']);
	}

	$container->setParameter('database_name', substr($db['path'], 1));
}

$container->setParameter('redis_dsn', getenv('REDIS_URL'));

echo 'Using DSN ' . getenv('REDIS_URL');

$redis = parse_url(getenv('REDIS_URL'));

if ($redis) {
	$container->setParameter('redis_dsn', getenv('REDIS_URL'));
	$container->setParameter('redis_user', $redis['user']);
	$container->setParameter('redis_password', $redis['pass']);
	$container->setParameter('redis_host', $redis['host']);

	if (isset($redis['port'])) {
		$container->setParameter('redis_port', $redis['port']);
	}

	if (isset($redis['path'])) {
		$container->setParameter('redis_database', substr($redis['path'], 1));
	}
}
