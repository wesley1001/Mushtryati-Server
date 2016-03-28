<?php
namespace App\Src\Utils;

use Exception;
use Intervention\Image\Facades\Image;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

abstract class BaseImageService
{

    private $uploadDir;

    private $thumbnailImagePath;

    private $largeImagePath;

    private $mediumImagePath;

    protected $largeImageWidth = '750';

    protected $largeImageHeight = '700';

    protected $mediumImageWidth = 'null';

    protected $mediumImageHeight = '500';

    protected $thumbnailImageWidth = '250';

    protected $thumbnailImageHeight = 'null';

    const IMAGE_EXTENSION = '.jpg';

    public function __construct()
    {
        $this->uploadDir          = public_path() . '/uploads/medias/images/';
        $this->largeImagePath     = $this->getUploadDir() . 'large/';
        $this->mediumImagePath    = $this->getUploadDir() . 'medium/';
        $this->thumbnailImagePath = $this->getUploadDir() . 'thumb/';
    }

    protected function process(File $image, $hashedName, array $imageDimensions = ['large'])
    {
        $hashedName = $hashedName.self::IMAGE_EXTENSION;

        foreach ($imageDimensions as $imageDimension) {
            switch ($imageDimension) {
                case 'large':
                    Image::make($image->getRealPath())->save($this->largeImagePath . $hashedName);
                    break;
                case 'medium':
                    Image::make($image->getRealPath())->resize($this->mediumImageWidth,
                        $this->mediumImageHeight,function($constraint) {
                            $constraint->aspectRatio();
                        })->save($this->mediumImagePath . $hashedName);
                    break;
                case 'thumb':
                    Image::make($image->getRealPath())->resize($this->thumbnailImageWidth,
                        $this->thumbnailImageHeight,function($constraint) {
                            $constraint->aspectRatio();
                        })->save($this->thumbnailImagePath . $hashedName);
                    break;
                default :
                    break;
            }
        }

        return $this;
    }

    /**
     * @return mixed
     */
    private function getUploadDir()
    {
        return $this->uploadDir;
    }

    /**
     * @return mixed
     */
    private function getLargeImagePath()
    {
        return $this->largeImagePath;
    }

    /**
     * @return mixed
     */
    private function getMediumImagePath()
    {
        return $this->mediumImagePath;
    }

    /**
     * @return mixed
     */
    private function getThumbnailImagePath()
    {
        return $this->thumbnailImagePath;
    }

    public function destroy($name)
    {
        if (file_exists($this->getThumbnailImagePath() . $name)) {
            unlink($this->getThumbnailImagePath() . $name);
        }
        if (file_exists($this->getMediumImagePath() . $name)) {
            unlink($this->getMediumImagePath() . $name);
        }
        if (file_exists($this->getLargeImagePath() . $name)) {
            unlink($this->getLargeImagePath() . $name);
        }
    }


}