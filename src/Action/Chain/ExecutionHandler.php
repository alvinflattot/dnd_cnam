<?php
namespace App\Action\Chain;

use App\Factory\ActionFactory;
use App\Model\Character;

class ExecutionHandler extends AbstractActionHandler
{
    public function handle(string $actionType, Character $actor, ?Character $target, array $context): array
    {
        $action = ActionFactory::create($actionType);
        $result = $action->execute($actor, $target, $context);
        $context['result'] = $result;
        return parent::handle($actionType, $actor, $target, $context);
    }
}