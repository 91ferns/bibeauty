<?php

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
		// remove starting /
		$path = preg_replace("#^[/]#", "", $rabbitConsumer['path']);
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
		$path = preg_replace("#^[/]#", "", $rabbitProducer['path']);
	} else {
		$path = '/';
	}

	$container->setParameter('rabbit_producer_vhost', $path);

}
