<?php

namespace Tests\Framework\Twig;

use DateTime;
use Framework\Twig\{TextExtension, TimeExtension};
use PHPUnit\Framework\TestCase;

class TimeExtensionTest extends TestCase
{
    /**
     * Summary of textExtension
     * @var TimeExtension
     */
    private $timeExtension;

    /**
     * Summary of setUp
     * @return void
     */
    public function setUp(): void
    {
        $this->timeExtension = new TimeExtension();
    }

    public function testDateFormat()
    {
        $format = 'd/m/Y H:i';
        $date = new DateTime();
        $result = '<span class="timeago" datetime="'
            . $date->format(DateTime::ISO8601) . '">'
            . $date->format($format) . '</span>';
        $this->assertEquals(
            expected: $result,
            actual: $this->timeExtension->ago(
                date: $date,
                format: $format
            )
        );
    }
}