<?php

namespace Framework\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TextExtension extends AbstractExtension
{
    /**
     * Summary of getFilters
     * @return TwigFilter[]
     */
    public function getFilters()
    {
        return [
            new TwigFilter('excerpt', [$this, 'excerpt'])
        ];
    }

    /**
     * Summary of excerpt
     * @param mixed $content
     * @param mixed $maxLength
     * @return string
     */
    public function excerpt(?string $content, $maxLength = 100): string
    {
        if ($content === null) {
            return '';
        }
        if (mb_strlen($content) > $maxLength) {
            $excerpt = mb_substr($content, 0, $maxLength);
            $lastSpace = mb_strrpos($excerpt, ' ');
            return mb_substr($excerpt, 0, $lastSpace) . '...';
        }
        return $content;
    }
}
