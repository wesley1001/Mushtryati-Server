<?php

namespace App\Src\Utils;

use Symfony\Component\HttpFoundation\File\File;

class ImageUploader extends BaseImageService implements MediaUploaderInterface
{

    public function upload(File $file,$hashedName)
    {
        return $this->process($file,$hashedName, ['large','thumb','medium']);
    }

}