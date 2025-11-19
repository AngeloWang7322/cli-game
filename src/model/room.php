<?php
class Room
{
    public $name;
    public array $path = [];
    public array $doors = [];
    public array $items = [];
    public ROLE $requiredRole;

    function __construct($name, $requiredRole = ROLE::WANDERER, array $path = [])
    {
        $this->name = $name;

        //if statement nur im development noetig
        if ($name != "hall") {
            $this->path = empty($path) ? $_SESSION["curRoom"]->path : $path;

            array_push($this->path, $name);
        }
        $this->requiredRole = $requiredRole;
    }
}
