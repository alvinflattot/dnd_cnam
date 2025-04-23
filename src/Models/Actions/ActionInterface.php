<?php
namespace App\Models\Actions;
use App\Models\Character;

require_once 'vendor/autoload.php';

interface ActionInterface {
    public function execute(Character $character): string;
}