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

$rabbitConsumer = parse_url(getenv('RABBITMQ_BIGWIG_RX_URL')); # Consumers

if ($rabbitConsumer) {
	$container->setParameter('rabbit_consumer_dsn', getenv('RABBITMQ_BIGWIG_RX_URL'));

	$container->setParameter('rabbit_consumer_user', $rabbitConsumer['user']);
	$container->setParameter('rabbit_consumer_password', $rabbitConsumer['pass']);
	$container->setParameter('rabbit_consumer_host', $rabbitConsumer['host']);

	if (isset($rabbitConsumer['port'])) {
		$container->setParameter('rabbit_consumer_port', $rabbitConsumer['port']);
	}

	if (isset($rabbitConsumer['path'])) {
		$path = $rabbitConsumer['path'];
	} else {
		$path = '/';
	}

	$container->setParameter('rabbit_consumer_vhost', $path);
}

$rabbitProducer = parse_url(getenv('RABBITMQ_BIGWIG_TX_URL'));  # Producers

if ($rabbitProducer) {
	$container->setParameter('rabbit_producer_dsn', getenv('RABBITMQ_BIGWIG_RX_URL'));

	$container->setParameter('rabbit_producer_user', $rabbitProducer['user']);
	$container->setParameter('rabbit_producer_password', $rabbitProducer['pass']);
	$container->setParameter('rabbit_producer_host', $rabbitProducer['host']);

	if (isset($rabbitProducer['port'])) {
		$container->setParameter('rabbit_producer_port', $rabbitProducer['port']);
	}

	if (isset($rabbitProducer['path'])) {
		$path = $rabbitProducer['path'];
	} else {
		$path = '/';
	}

	$container->setParameter('rabbit_producer_vhost', $path);

}
