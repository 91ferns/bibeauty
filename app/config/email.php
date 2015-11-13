<?php

$sendgrid_host = getenv('SENDGRID_HOST');

if (!$sendgrid_host) {
    $sendgrid_host = 'smtp.sendgrid.net';
}

$container->setParameter('mailer_host', $sendgrid_host);
$container->setParameter('mailer_user', getenv('SENDGRID_USERNAME') );
$container->setParameter('mailer_password', getenv('SENDGRID_PASSWORD') );
