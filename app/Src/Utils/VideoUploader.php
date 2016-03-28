<?php

namespace App\Src\Utils;

use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFMpeg;
use Symfony\Component\HttpFoundation\File\File;

class VideoUploader implements MediaUploaderInterface
{

    const FFMPEG_BINARY = '/home/vagrant/bin/ffmpeg';
    const FFPROBE_BINARY = '/home/vagrant/bin/ffprobe';
    const IMAGE_EXTENSION = '.jpg';
    const VIDEO_EXTENSION = '.mov';

    private $frameFileName;

    public function __construct()
    {
        $this->uploadDir   = public_path() . '/uploads/medias/videos/';
    }

    /**
     * @param $file
     * @param $hashedName
     * @return string .. the path of uploaded frame image
     */
    public function upload(File $file,$hashedName)
    {
        $ffmpeg = FFMpeg::create([
            'ffmpeg.binaries'  => self::FFMPEG_BINARY,
            'ffprobe.binaries' => self::FFPROBE_BINARY
        ]);
        $uploadedFile = $file->move($this->uploadDir,$hashedName);
        $this->setFrameFileName($this->uploadDir.$hashedName.self::IMAGE_EXTENSION);
        $video = $ffmpeg->open($uploadedFile);
        $video->frame(TimeCode::fromSeconds(1))->save($this->getFrameFileName());
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFrameFileName()
    {
        return $this->frameFileName;
    }

    /**
     * @param mixed $frameFileName
     */
    public function setFrameFileName($frameFileName)
    {
        $this->frameFileName = $frameFileName;
    }
}