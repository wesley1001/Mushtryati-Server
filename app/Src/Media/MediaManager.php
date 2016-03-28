<?php

namespace App\Src\Media;

use App\Src\Utils\ImageUploader;
use App\Src\Utils\VideoUploader;
use Exception;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class MediaManager
{

    private $file;
    private $imageExtensions = ['jpg','png'];
    private $videoExtensions = ['mov'];
    private $hashedName;
    private $mediaExtension;
    private $mediaType;

    /**
     * MediaManager constructor.
     * @param UploadedFile $file
     */
    public function __construct(UploadedFile $file)
    {
        $this->file = $file;
    }


    /**
     * @return $this
     * @throws Exception
     * //todo: Refactor this class to be accept more video types
     */
    public function storeMedia()
    {
        if(!isset($this->file)) {
            throw  new Exception('invalid file');
        }
        $media = $this->file;
        $this->setMediaExtension($media->getClientOriginalExtension());
        $this->hashName();
        $imageUploader = new ImageUploader();
        $videoUploader = new VideoUploader();

        if(in_array($this->getMediaExtension(),$this->imageExtensions)) {
            $this->setMediaType('image');
            $imageUploader->upload($media,$this->getHashedName());
        } elseif(in_array($this->getMediaExtension(),$this->videoExtensions)) {
            $this->setMediaType('video');
            $uploadedVideo = $videoUploader->upload($media,$this->getHashedName());
            $frameImage = new File($uploadedVideo->getFrameFileName());
            $imageUploader->upload($frameImage,$this->getHashedName());
            unlink($uploadedVideo->getFrameFileName());
        }
        return $this;
    }

    /**
     * @return mixed
     */
    public function getHashedName()
    {
        return $this->hashedName;
    }

    /**
     * @param mixed $hashedName
     */
    private function setHashedName($hashedName)
    {
        $this->hashedName = $hashedName;
    }

    /**
     * @return mixed
     */
    public function getMediaExtension()
    {
        return $this->mediaExtension;
    }

    /**
     * @param mixed $mediaExtension
     */
    private function setMediaExtension($mediaExtension)
    {
        $this->mediaExtension = $mediaExtension;
    }

    private function hashName()
    {
        $hashed = md5(uniqid(rand() * (time())));
        $this->setHashedName($hashed);
    }

    /**
     * @return mixed
     */
    public function getMediaType()
    {
        return $this->mediaType;
    }

    /**
     * @param mixed $mediaType
     */
    private function setMediaType($mediaType)
    {
        $this->mediaType = $mediaType;
    }

    public function getHashedNameWithExtension()
    {
        $extension = $this->getMediaExtension();
        return $this->getHashedName().'.'.$extension;
    }
}