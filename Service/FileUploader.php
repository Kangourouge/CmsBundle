<?php

namespace KRG\CmsBundle\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader
{
    private $targetDirectory;

    public function __construct($targetDirectory)
    {
        $this->targetDirectory = $targetDirectory;
    }

    public function upload(UploadedFile $file)
    {
        $filename = md5(uniqid()).'.'.$file->guessExtension();
        $file->move($this->getTargetDirectory(), $filename);

        return $filename;
    }

    public function getTargetDirectory()
    {
        return $this->targetDirectory;
    }
}
