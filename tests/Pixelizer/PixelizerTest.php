<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Pixelizer\Pixelizer;

class PixelizerTest extends TestCase
{
    static public $originalDataHash;
    static public $encodedFilePath;

    public function testCanEncode()
    {
        $data = sha1('test');

        $pixelizer = $this->getNewInstance($data);

        self::$encodedFilePath = tempnam(sys_get_temp_dir(), 'pxlzr_');
        $pixelizer->encode(self::$encodedFilePath);

        self::$originalDataHash = hash_file('sha3-512', self::$encodedFilePath);

        $this->assertTrue(file_exists(self::$encodedFilePath));
        $this->assertGreaterThan(0, filesize(self::$encodedFilePath));

        $image_info = getimagesize(self::$encodedFilePath);

        $this->assertEquals(IMAGETYPE_PNG, $image_info[2]);
    }

    public function testCanDecode()
    {
        $pixelizer = $this->getNewInstance();
        $pixelizer->loadImage(self::$encodedFilePath);

        $decodedFilePath = tempnam(sys_get_temp_dir(), 'pxlzr_');

        $pixelizer->decode($decodedFilePath);

        $this->assertEquals(self::$originalDataHash, hash_file('sha3-512', self::$encodedFilePath));
    }

    public function getNewInstance($data = null): Pixelizer
    {
        $pixelizer = new Pixelizer($data);

        $this->assertInstanceOf(Pixelizer::class, $pixelizer);

        return $pixelizer;
    }
}
