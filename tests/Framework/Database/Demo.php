<?php
namespace Tests\Framework\Database;

use AllowDynamicProperties;

#[AllowDynamicProperties()]
class Demo
{
    private $slug;
    public function getSlug()
    {
        return $this->slug;
    }

    public function setSlug(string $slug)
    {
        $this->slug = "$slug\demo";
    }
}