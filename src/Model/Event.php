<?php
namespace App\Model;

class Event
{
    public function __construct(private string $msg) {}

    public function getMessage(): string
    {
        return $this->msg;
    }
}
