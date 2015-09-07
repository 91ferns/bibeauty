<?php

// src/AppBundle/Services/AWS.php
namespace AppBundle\Services;

use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Aws\S3\S3Client;
use Aws\S3\MultipartUploader;
use Aws\Exception\MultipartUploadException;

class AWSFactory
{

    public static function createAWSFactory($config, LoggerInterface $logger)
    {
        return new AWSFactory($config, $container);

    }

    protected $config;
    protected $logger;

    public function __construct($config, LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->config = $config;
    }

    protected function S3() {
        return new S3Client(array(
            'version' => $this->config['version'],
            'region' => $this->config['region'],
            'credentials' => $this->config['credentials']
        ));
    }

    public function upload($path, $filename = null, $acl = 'public-read') {
        $s3 = $this->S3();

        if ($filename === null) {
            $filename = basename($path);
        } else {
            if (is_array($filename)) {
                $filename = join(PATH_SEPARATOR, $filename);
            }
        }

        $uploader = new MultipartUploader(
            $s3,
            $path,
            array(
                'bucket' => $this->config['bucket'],
                'key' => $filename,
                'ACL' => $acl
            )
        );

        try {
            $result = $uploader->upload();
        } catch (MultipartUploadException $e) {
            $this->logger->err($e->getMessage());
            if ($params = $e->getState()->getId()) {
                if (!empty($params['UploadId']))
                    $result = $s3->abortMultipartUpload($params);
                else $result = $e;
            }
        }

        return $result;
    }

    // ...
}
