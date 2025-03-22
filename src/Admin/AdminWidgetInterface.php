<?php

namespace App\Admin;

interface AdminWidgetInterface
{
    public function renderMenu(): string;
    public function render(): string;
}
