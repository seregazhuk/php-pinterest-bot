<?php

namespace seregazhuk\PinterestBot\Helpers;

class FileHelper
{
    public static function getMimeType($filePath)
    {
        $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
        $type = finfo_file($fileInfo, $filePath);
        finfo_close($fileInfo);

        return $type;
    }
}