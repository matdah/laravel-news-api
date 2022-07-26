<?php

namespace App\Lib;

class Image
{
    private $imagepath;
    private $width_thumbnail;
    private $height_thumbnail;
    private $jpeg_quality;
    private $webp_quality;

    public function __construct(
        $imagepath = 'images/',
        $width_thumbnail = 500,
        $height_thumbnail = 400,
        $jpeg_quality = 80,
        $webp_quality = 60
    ) {
        $this->imagepath = $imagepath;
        $this->width_thumbnail = $width_thumbnail;
        $this->height_thumbnail = $height_thumbnail;
        $this->jpeg_quality = $jpeg_quality;
        $this->webp_quality = $webp_quality;
    }

    public function uploadImage(array $image): bool
    {
        $success = true;
        if (!$this->setImage($image)) {
            $success = false;
        }
        return true;
    }

    /**
     * Set image-file (JPEG)
     * @param file $image
     * @return string filename
     */
    public function setImage($image): string
    {
        if ($this->isImageAllowed($image)) {
            // Create filename
            $filename = $this->createFileName();

            //Flyttar filen till rätt katalog      
            move_uploaded_file($image["tmp_name"], $this->imagepath . $filename);

            //Spar namn på originalbild och miniatyr i variabler
            $storedfile = $filename;
            $thumbnail = "thumb_" . $filename;

            //Maximal storlek i höjd och bredd för miniatyr
            $width_thumbnail = $this->width_thumbnail;
            $height_thumbnail = $this->height_thumbnail;

            //Läser in originalstorleken på den uppladdade bilden, och spar 
            //den i variablerna width_orig, height_orig
            list($width_thumbnail_orig, $height_thumbnail_orig) = getimagesize($this->imagepath . $storedfile);

            //Räknar ut förhållandet mellan höjd och bredd (sk "ratio")
            //Detta för att kunna få samma höjd- breddförhållande på miniatyren
            $ratio_orig = $width_thumbnail_orig / $height_thumbnail_orig;

            //Räknar ut storlek på miniatyr
            if ($width_thumbnail / $height_thumbnail > $ratio_orig) {
                $width_thumbnail = $height_thumbnail * $ratio_orig;
                $height_thumbnail = $width_thumbnail / $ratio_orig;
            } else {
                $height_thumbnail = $width_thumbnail / $ratio_orig;
                $width_thumbnail = $height_thumbnail * $ratio_orig;
            }

            // Konvertera till heltal
            $width_thumbnail = (int)$width_thumbnail;
            $height_thumbnail = (int)$height_thumbnail;

            // Skapar WebP-bild
            if (function_exists("imagewebp")) {
                $image_webp = imagecreatefromjpeg($this->imagepath . $storedfile);
                $filename_webp = pathinfo($storedfile)['filename'] . '.webp';
                imagewebp($image_webp, $this->imagepath . $filename_webp, $this->webp_quality);
            }

            //Skapar en ny bild miniatyrbild 
            $image_p = imagecreatetruecolor($width_thumbnail, $height_thumbnail);
            $image = imagecreatefromjpeg($this->imagepath . $storedfile);
            imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width_thumbnail, $height_thumbnail, $width_thumbnail_orig, $height_thumbnail_orig);

            //Sparar miniatyr - JPEG
            imagejpeg($image_p, $this->imagepath . $thumbnail, $this->jpeg_quality);

            // Sparar miniatyr - WebP
            if (function_exists("imagewebp")) {
                $filename_webp = pathinfo($thumbnail)['filename'] . '.webp';
                imagewebp($image_p, $this->imagepath . $filename_webp, $this->webp_quality);
            }

            return $storedfile;
        } else {
            return null;
        }
    }

    /**
     * Check if image file is allowed
     * @param file $image
     * @return bool
     */
    public function isImageAllowed($image)
    {
        $type = $image['type'];
        if ($type != "image/jpeg") {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Generate non-taken filenamn
     * @return string $filename
     */
    public function createFileName()
    {
        do {
            $random_filename = substr(str_shuffle(str_repeat("0123456789abcdefghijklmnopqrstuvwxyz", 12)), 0, 12);
            $random_filename = $random_filename . ".jpg";
        } while (!$this->FilenameAvailable(($random_filename)));

        return $random_filename;
    }

    /**
     * Check if filename alreade exist
     * @param string $filename
     * @return bool
     */
    public function FilenameAvailable($filename)
    {
        if (file_exists($this->imagepath . $filename)) {
            return false;
        } else {
            return true;
        }
    }
}
