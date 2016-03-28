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
    public $uploadDir;
    public $largeImagePath;
    public $mediumImagePath;
    public $thumbnailImagePath;
    public $videoPath;
    const IMAGE_EXTENSION = 'jpg';
    const VIDEO_EXTENSION = 'mov';

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
        if(in_array($this->getMediaExtension(),$this->imageExtensions)) {
            $this->setMediaType('image');
            $this->uploadImage($media);
        } elseif(in_array($this->getMediaExtension(),$this->videoExtensions)) {
            $this->setMediaType('video');
            $this->uploadVideo($media);
        }
//        dd($this);
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

        if($this->getMediaType() == 'video') {
            $extension = 'mov';
        }

        return $this->getHashedName().'.'.$extension;
    }

    /**
     * @param $media
     */
    public function uploadImage($media)
    {
        $imageUploader = new ImageUploader();
        if (is_null($this->getMediaExtension()) || !in_array($this->getMediaExtension(), $this->imageExtensions)) {
            $this->setMediaExtension(self::IMAGE_EXTENSION);
        }
        $hashedNamed = $this->getHashedName() .'.'. $this->getMediaExtension();
        $imageUploader->upload($media, $hashedNamed);
        $this->largeImagePath     = env('LARGE_IMAGE_PATH').$hashedNamed;
        $this->mediumImagePath    = env('MEDIUM_IMAGE_PATH').$hashedNamed;
        $this->thumbnailImagePath = env('THUMB_IMAGE_PATH').$hashedNamed;
        return $this;
    }

    /**
     * @param $media
     */
    public function uploadVideo($media)
    {
        $videoUploader = new VideoUploader();
        if (is_null($this->getMediaExtension()) || !in_array($this->getMediaExtension(), $this->videoExtensions)) {
            $this->setMediaExtension(self::VIDEO_EXTENSION);
        }
        $hashedNamed = $this->getHashedName() .'.'. $this->getMediaExtension();
        $uploadedVideo = $videoUploader->upload($media, $hashedNamed);
        $frameImage = new File($uploadedVideo->getFrameFileName());
        $this->uploadImage($frameImage);
        unlink($uploadedVideo->getFrameFileName());
        $this->videoPath = env('THUMB_IMAGE_PATH').$hashedNamed;
    }
}