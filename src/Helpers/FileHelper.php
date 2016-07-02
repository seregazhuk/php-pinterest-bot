<?php

namespace seregazhuk\PinterestBot\Helpers;

use seregazhuk\PinterestBot\Exceptions\InvalidRequestException;

class FileHelper
{
    /**
     * @param string $filePath
     * @return mixed
     * @throws InvalidRequestException
     */
    public static function getMimeType($filePath)
    {
        if (!file_exists($filePath)) {
            throw new InvalidRequestException("$filePath: failed to open file.");
        }
        
        $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
        $type = finfo_file($fileInfo, $filePath);
        finfo_close($fileInfo);

        return $type;
    }
}