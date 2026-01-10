<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class NoticeCard extends Component
{
    public string $text;
    public string $color;

    public function __construct($text, $color)
    {
        $this->text = $text;
        $this->color = $color;
    }

    public function render(): View|Closure|string
    {
        return view('components.notice-card');
    }
}
