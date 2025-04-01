<?php
namespace App\Blog;

use Framework\Upload;

class PostUpload extends Upload
{

    public function __construct()
    {
        $this->formats = [
            'thumb' => [320, 180]
        ];
        $this->path = 'public' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'posts';
        parent::__construct($this->path);
    }
}
