<?php
namespace App\Action\Chain;

use App\Dispatcher\EventDispatcher;
use App\Model\Character;
use App\Model\Event;

class EventDispatchHandler extends AbstractActionHandler
{
    public function handle(string $actionType, Character $actor, ?Character $target, array $context): array
    {
        $output = $context['result'] ?? [];
        if (isset($output['events']) && is_array($output['events'])) {
            $dispatcher = EventDispatcher::getInstance();
            foreach ($output['events'] as $message) {
                $dispatcher->dispatch(new Event($message));
            }
        }
        return $output;
    }
}