<?php
/**
 * Created by PhpStorm.
 * User: ZaL
 * Date: 3/28/16
 * Time: 10:38 PM
 */

namespace App\Src\Utils;


use Symfony\Component\HttpFoundation\File\File;

interface MediaUploaderInterface
{

    public function upload(File $file,$hashedName);

}