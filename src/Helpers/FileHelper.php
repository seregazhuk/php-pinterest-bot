<?php

namespace seregazhuk\PinterestBot\Helpers;

use seregazhuk\PinterestBot\Exceptions\InvalidRequest;

class FileHelper
{
    /**
     * @param string $filePath
     * @return mixed
     * @throws InvalidRequest
     */
    public static function getMimeType($filePath)
    {
        if (!file_exists($filePath)) {
            throw new InvalidRequest("$filePath: failed to open file.");
        }
        
        $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
        $type = finfo_file($fileInfo, $filePath);
        finfo_close($fileInfo);

        return $type;
    }

    /**
     * @param string $source
     * @param string $destination
     */
    public static function saveTo($source, $destination)
    {
        copy($source, $destination);
    }
}
