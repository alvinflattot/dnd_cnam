<?php
namespace App\Observer;

use App\Model\Event;

class LoggingListener implements EventListenerInterface
{
    public function handle(object $event): void
    {
        if ($event instanceof Event) {
            file_put_contents(
                __DIR__ . '/../../events.log',
                $event->getMessage() . "\n",
                FILE_APPEND
            );
        }
    }
}
