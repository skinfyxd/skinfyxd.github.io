<?php

namespace App\Events;

class ConfigureAdminMenu extends Event
{
    public $menu;

    /**
     * Create a new event instance.
     *
     * @param  array $menu
     * @return void
     */
    public function __construct(array &$menu)
    {
        // Pass array by reference
        $this->menu = &$menu;
    }
}
