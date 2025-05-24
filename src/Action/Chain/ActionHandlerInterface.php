<?php
namespace App\Action\Chain;

use App\Model\Character;

interface ActionHandlerInterface
{
    public function setNext(ActionHandlerInterface $handler): ActionHandlerInterface;
    public function handle(string $actionType, Character $actor, ?Character $target, array $context): array;
}