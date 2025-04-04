<?php
namespace Framework;

use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Psr\Http\Message\UploadedFileInterface;

class Upload
{
    protected $path;
    protected $formats;

    /**
     * @param mixed $path
     */
    public function __construct(?string $path)
    {
        $this->path = $path;
    }


    /**
     * @param \Psr\Http\Message\UploadedFileInterface $file
     * @param mixed $oldFile
     * @return mixed|string|null
     */
    public function upload(UploadedFileInterface $file, ?string $oldFile = null)
    {
        if ($file->getError() === UPLOAD_ERR_OK) {
            $this->delete($oldFile);
            $targetPath = $this->addCopySuffix(
                $this->path . DIRECTORY_SEPARATOR . $file->getClientFilename()
            );
            $dirname = pathinfo($targetPath, PATHINFO_DIRNAME);
            if (!file_exists($dirname)) {
                mkdir($dirname, 777, true);
            }
            $file->moveTo($targetPath);
            $this->generateFormats($targetPath);
            return pathinfo($targetPath)['basename'];
        }
        return null;
    }

    /**
     * Summary of addSuffix
     * @param string $targetPath
     */
    private function addCopySuffix(string $targetPath)
    {
        if (file_exists($targetPath)) {
            return $this->addCopySuffix($this->getPathWithSuffix(
                path: $targetPath,
                suffix: 'copy'
            ));
        }
        return $targetPath;
    }

    /**
     * Summary of delete
     * @param string $oldFile
     * @return void
     */
    public function delete(?string $oldFile): void
    {
        if ($oldFile) {
            $oldFile = $this->path . DIRECTORY_SEPARATOR . $oldFile;
            if (file_exists($oldFile)) {
                unlink($oldFile);
            }
            foreach ($this->formats as $format => $_) {
                $oldFileWithFormat = $this->getPathWithSuffix($oldFile, $format);
                if (file_exists($oldFileWithFormat)) {
                    unlink($oldFileWithFormat);
                }
            }
        }
    }

    private function getPathWithSuffix(string $path, string $suffix)
    {
        $info = pathinfo($path);
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        return $info['dirname'] . DIRECTORY_SEPARATOR .
            $info['filename'] . '_' . $suffix . '.' . $extension;
    }

    /**
     * Summary of generateFormats
     * @param mixed $targetPath
     * @return void
     */
    private function generateFormats($targetPath)
    {
        if ($this->formats) {
            foreach ($this->formats as $format => $size) {
                $manager = new ImageManager(new Driver());
                $destination = $this->getPathWithSuffix($targetPath, $format);
                [$width, $height] = $size;
                $manager->read($targetPath)
                    ->scale($width, $height)
                    ->save($destination);
            }
        }
    }
}
