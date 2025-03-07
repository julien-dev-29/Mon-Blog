<?php

namespace Framework\Twig;

use DateTime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TimeExtension extends AbstractExtension
{
    /**
     * Summary of getFilters
     * @return TwigFilter[]
     */
    public function getFilters()
    {
        return [
            new TwigFilter('ago', [$this, 'ago'], ['is_safe' => ['html']])
        ];
    }

    /**
     * Summary of ago
     * @param \DateTime $date
     * @param string $format
     * @return string
     */
    public function ago(DateTime $date, string $format = 'd/m/Y H:i')
    {
        return '<span class="timeago" datetime="'
            . $date->format(DateTime::ISO8601) . '">'
            . $date->format($format) . '</span>';
    }
}
