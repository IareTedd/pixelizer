<?php
/**
 * Author:      Tedd
 * Created:     8/16/2020
 * License:     MIT
 */

namespace Pixelizer;

use \Exception;

class Pixelizer
{
    const EOL = "\n";

    protected $dataSourceSize = 0;
    protected $data = '';
    protected $image;

    /**
     * Pixelizer constructor.
     * @param $data
     */
    public function __construct($data = null)
    {
        if ($data !== null) {
            $this->setData($data);
        }
    }

    public function setData($data)
    {
        $this->dataSourceSize = strlen($data);
        $this->data = base64_encode($data);

        while (strlen(bin2hex($this->data)) % 6 > 0) {
            $this->data .= self::EOL;
        }

        $this->data = bin2hex($this->data);
    }

    /**
     * @param $filename
     * @return bool
     * @throws Exception
     */
    public function loadImage(string $filename): bool
    {
        if (!file_exists($filename)) {
            throw new Exception('File not found.');
        }

        if (IMAGETYPE_PNG === exif_imagetype($filename)) {
            $this->image = imagecreatefrompng($filename);

            return $this->image !== false;
        }

        throw new Exception('Unexpected image type.');
    }

    /**
     * @param $filename
     * @return bool
     * @throws Exception
     */
    public function encode(string $filename): bool
    {
        $pixels = [];
        $s = sqrt((int)preg_match_all('/[\da-z]{6}/', $this->data, $pixels));
        $w = $h = ceil($s);

        $this->image = imagecreatetruecolor($w, $h);
        imagealphablending($this->image, false);

        imagefill($this->image, 0, 0, 0x7f000000);

        $x = $y = 0;

        foreach ($pixels[0] as $idx => $hex) {
            $color = intval($hex, 16);
            $setpixel = imagesetpixel(
                $this->image,
                $x,
                $y,
                $color
            );

            if ($setpixel === false) {
                throw new Exception('imagesetpixel() has failed.');
            }

            if (++$x >= $w) {
                $y++;
                $x = 0;
            }
        }

        unset($pixels);

        imagesavealpha($this->image,true);
        return imagepng($this->image, $filename);
    }

    /**
     * @param $filename
     * @return bool
     * @throws Exception
     */
    public function decode(string $filename): bool
    {
        if (!$this->image) {
            throw new Exception('No image to decode. Make sure you have called loadImage() before trying to decode an image.');
        }

        $w = imagesx($this->image);
        $h = imagesy($this->image);

        $data = '';
        $alphaPixels=0;
        for ($y = 0; $y < $h; $y++) {
            for ($x = 0; $x < $w; $x++) {
                $pixel = imagecolorat($this->image, $x, $y);

                if ($pixel === false) {
                    throw new Exception(sprintf('Unable to get pixel color at [%d, %d]', $x, $y));
                }

                // Skip transparent pixels in the last row
                if ($pixel === 0x7f000000) {
                    $alphaPixels++;
                    break;
                }

                $data .= dechex($pixel);
            }
        }

        $data = str_replace(self::EOL, '', hex2bin($data));

        return file_put_contents($filename, base64_decode($data)) !== false;
    }
}