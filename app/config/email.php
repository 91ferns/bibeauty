<?php

$container->setParameter('mailer_host', getenv('SENDGRID_HOST') );
$container->setParameter('mailer_user', getenv('SENDGRID_USERNAME') );
$container->setParameter('mailer_password', getenv('SENDGRID_PASSWORD') );
