<?php

namespace Tests\Framework\Twig;

use Framework\Twig\TextExtension;
use PHPUnit\Framework\TestCase;

class TextExtensionTest extends TestCase
{
    /**
     * Summary of textExtension
     * @var TextExtension
     */
    private $textExtension;

    /**
     * Summary of setUp
     * @return void
     */
    public function setUp(): void
    {
        $this->textExtension = new TextExtension();
    }

    public function testExcerptWithShortText()
    {
        $text = "Salut";
        $this->assertEquals(
            expected: "Salut",
            actual: $this->textExtension->excerpt(
                content: $text,
                maxLength: 10
            )
        );
    }

    public function testExcerptWithLongText()
    {
        $text = "Salut les gens";
        $this->assertEquals(
            expected: "Salut...",
            actual: $this->textExtension->excerpt(
                content: $text,
                maxLength: 7
            )
        );
        $this->assertEquals(
            expected: "Salut les...",
            actual: $this->textExtension->excerpt(
                content: $text,
                maxLength: 12
            )
        );
    }
}