<?php

namespace App\Blog\Entity;

use AllowDynamicProperties;
use DateTime;

#[AllowDynamicProperties()]
class Post
{
    public $id;
    public $name;
    public $slug;
    public $content;
    public $createdAt;
    public $updatedAt;
    public $image;

    public function setCreatedAt(DateTime $datetime)
    {
        if (is_string($datetime)) {
            $this->createdAt = new DateTime(datetime: $datetime);
        }
    }

    public function setUpdatedAt(DateTime $datetime)
    {
        if (is_string($datetime)) {
            $this->updatedAt = new DateTime(datetime: $datetime);
        }
    }

    public function getThumb()
    {
        $filename = pathinfo($this->image, PATHINFO_FILENAME);
        $extension = pathinfo($this->image, PATHINFO_EXTENSION);
        return DIRECTORY_SEPARATOR . 'uploads' .
            DIRECTORY_SEPARATOR . 'posts' .
            DIRECTORY_SEPARATOR . $filename . '_thumb.' . $extension;
    }
}
