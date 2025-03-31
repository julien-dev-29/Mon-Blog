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
    public $categoryId;
    public $categoryName;

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
}
