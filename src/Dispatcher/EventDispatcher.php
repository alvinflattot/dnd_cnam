<?php
namespace App\Dispatcher;

use App\Observer\EventListenerInterface;

class EventDispatcher
{
    private static ?self $instance = null;
    /** @var EventListenerInterface[] */
    private array $listeners = [];

    private function __construct() {}

    public static function getInstance(): self
    {
        return self::$instance ??= new self();
    }

    public function addListener(EventListenerInterface $l): void
    {
        $this->listeners[] = $l;
    }

    public function dispatch(object $evt): void
    {
        foreach ($this->listeners as $l) {
            $l->handle($evt);
        }
    }
}
