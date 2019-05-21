<?php

declare(strict_types=1);

namespace Hirss\commands\custom;

use pocketmine\utils\TextFormat;

class Mail{

    public const prefix = TextFormat::GOLD . "MevMail.com >> " . TextFormat::WHITE;

    private $sender;
    private $date;
    private $msg;
    private $id;

    public function __construct(Person $sender, string $date, string $message, int $id){
        $this->sender = $sender;
        $this->date = $date;
        $this->msg = $message;
        $this->id = $id;
    }

    public function getSender() : Person{
        return $this->sender;
    }

    public function getDate() : string{
        return $this->date;
    }

    public function getMsg() : string{
        return $this->msg;
    }

    public function getId() : int{
        return $this->id;
    }


}