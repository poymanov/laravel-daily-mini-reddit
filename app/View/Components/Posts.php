<?php

namespace App\View\Components;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\View\Component;

class Posts extends Component
{
    public LengthAwarePaginator $posts;

    public int $postsTextPreviewLimit;

    /**
     * @param LengthAwarePaginator $posts
     * @param int                  $postsTextPreviewLimit
     */
    public function __construct(LengthAwarePaginator $posts, int $postsTextPreviewLimit)
    {
        $this->posts                 = $posts;
        $this->postsTextPreviewLimit = $postsTextPreviewLimit;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.posts');
    }
}
