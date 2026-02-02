<?php

namespace App\Services;

class Breadcrumbs
{
    protected $crumbs = [];
    protected $divider = '>';
    protected $lastItemWithHref = false;

    public function addCrumb($text, $url = null)
    {
        $this->crumbs[] = ['text' => $text, 'url' => $url];
        return $this;
    }

    public function setDivider($divider)
    {
        $this->divider = $divider;
        return $this;
    }

    public function setLastItemWithHref($bool)
    {
        $this->lastItemWithHref = $bool;
        return $this;
    }

    public function render()
    {
        // Using Bootstrap 4 breadcrumb class, which handles dividers via CSS usually.
        // If specific divider support is needed via inline style or custom usage,
        // we might need to adjust, but standard Bootstrap 4 is safe.
        // We will ignore $this->divider since Bootstrap 4 uses CSS for that.
        
        $html = '<nav aria-label="breadcrumb">';
        $html .= '<ol class="breadcrumb">';

        foreach ($this->crumbs as $key => $crumb) {
            $isLast = ($key === count($this->crumbs) - 1);
            $isActive = $isLast ? ' active' : '';

            $html .= '<li class="breadcrumb-item' . $isActive . '">';
            
            if ($crumb['url'] && (!$isLast || $this->lastItemWithHref)) {
                $html .= '<a href="' . $crumb['url'] . '">' . $crumb['text'] . '</a>';
            } else {
                $html .= $crumb['text']; // Allowing HTML content as per usage
            }
            
            $html .= '</li>';
        }

        $html .= '</ol>';
        $html .= '</nav>';

        return $html;
    }
}
