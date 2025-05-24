<?php
namespace App\Action\Chain;

use App\Model\Character;

abstract class AbstractActionHandler implements ActionHandlerInterface
{
    protected ?ActionHandlerInterface $next = null;

    public function setNext(ActionHandlerInterface $handler): ActionHandlerInterface
    {
        $this->next = $handler;
        return $handler;
    }

    public function handle(string $actionType, Character $actor, ?Character $target, array $context): array
    {
        if ($this->next) {
            return $this->next->handle($actionType, $actor, $target, $context);
        }
        return [];
    }
}