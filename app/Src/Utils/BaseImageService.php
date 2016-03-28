<?php
namespace App\Src\Utils;

use Intervention\Image\Facades\Image;
use Symfony\Component\HttpFoundation\File\File;

abstract class BaseImageService
{

    private $uploadDir;

    private $thumbnailImagePath;

    private $largeImagePath;

    private $mediumImagePath;

    protected $largeImageWidth = 'null';

    protected $largeImageHeight = '1800';

    protected $mediumImageWidth = 'null';

    protected $mediumImageHeight = '800';

    protected $thumbnailImageWidth = '250';

    protected $thumbnailImageHeight = 'null';

//    const IMAGE_EXTENSION = '.jpg';

    public function __construct()
    {
        $this->uploadDir          = public_path().'/uploads/medias';
        $this->largeImagePath     = $this->getUploadDir().env('LARGE_IMAGE_PATH');
        $this->mediumImagePath    = $this->getUploadDir().env('MEDIUM_IMAGE_PATH');
        $this->thumbnailImagePath = $this->getUploadDir().env('THUMB_IMAGE_PATH');
    }

    protected function process(File $image, $hashedName, array $imageDimensions = ['large'])
    {
//        $hashedName = $hashedName.self::IMAGE_EXTENSION;
        $hashedName = $hashedName;

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