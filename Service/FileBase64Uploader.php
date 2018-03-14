<?php

namespace KRG\CmsBundle\Service;

class FileBase64Uploader
{
    private $rootDir;
    private $uploadDir;

    public function __construct($rootDir)
    {
        $this->rootDir = $rootDir;
        $this->uploadDir = '/uploads/cms/';
    }

    public function uploadBase64($base64)
    {
        if (!is_string($base64) || !preg_match('/^data:(image\/([a-z]+));base64,(.*)$/', $base64, $matches)) {
            throw new \InvalidArgumentException('Base64 string format required');
        }

        $mimeType = $matches[1];
        $extension = $matches[2];
        $data = $matches[3];

        $filename = sprintf('%s.%s', md5(uniqid()), $extension);
        $path = sprintf('%s/%s', $this->getUploadDirectory(), $filename);

        if (!is_dir($this->getUploadDirectory())) {
            mkdir($this->getUploadDirectory(), 0755, true);
        }

        $ret = file_put_contents($path, base64_decode($data));

        return $ret ? sprintf('%s/%s', $this->uploadDir, $filename) : '';
    }

    public function getUploadDirectory()
    {
        return $this->rootDir.'/../web'.$this->uploadDir;
    }
}
