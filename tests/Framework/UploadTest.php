<?php
namespace Tests\Framework;

use App\Blog\PostUpload;
use Framework\Upload;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UploadedFileInterface;

class UploadTest extends TestCase
{

    /**
     * @var Upload
     */
    private $upload;

    public function setUp(): void
    {
        $this->upload = new Upload('tests');
    }
    public function tearDown(): void
    {
        if (file_exists('tests' . DIRECTORY_SEPARATOR . 'demo.jpg')) {
            unlink('tests' . DIRECTORY_SEPARATOR . 'demo.jpg');
        }
    }

    public function testUpload()
    {
        $uploadedFile = $this->createMock(UploadedFileInterface::class);
        $uploadedFile->expects($this->any())
            ->method('getClientFileName')
            ->willReturn('demo.jpg');
        $uploadedFile->expects($this->once())
            ->method('moveTo')
            ->with($this->equalTo('tests' . DIRECTORY_SEPARATOR . 'demo.jpg'));
        $this->assertEquals('demo.jpg', $this->upload->upload($uploadedFile));
    }

    public function testUploadWithExistingFile()
    {
        $uploadedFile = $this->createMock(UploadedFileInterface::class);
        $uploadedFile->expects($this->any())
            ->method('getClientFileName')
            ->willReturn('demo.jpg');
        touch('tests' . DIRECTORY_SEPARATOR . 'demo.jpg');
        $uploadedFile->expects($this->once())
            ->method('moveTo')
            ->with($this->equalTo('tests' . DIRECTORY_SEPARATOR . 'demo_copy.jpg'));
        $this->assertEquals('demo_copy.jpg', $this->upload->upload($uploadedFile));
    }
}