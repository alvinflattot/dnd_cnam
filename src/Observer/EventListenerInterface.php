<?php
namespace App\Observer;

interface EventListenerInterface
{
    public function handle(object $event): void;
}
