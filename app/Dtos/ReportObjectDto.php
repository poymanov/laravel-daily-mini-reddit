<?php

declare(strict_types=1);

namespace App\Dtos;

class ReportObjectDto
{
    /** @var string|null */
    private ?string $title;

    /** @var string|null */
    private ?string $content;

    /**
     * @param string|null $title
     * @param string|null $content
     */
    public function __construct(?string $title, ?string $content)
    {
        $this->title   = $title;
        $this->content = $content;
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @return string|null
     */
    public function getContent(): ?string
    {
        return $this->content;
    }
}
