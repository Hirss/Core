<?php

namespace Hirss\commands\override;


use Hirss\Main;
use Hirss\utils\Prefix;
use pocketmine\command\CommandSender;
use Hirss\commands\CoreCommand;
use pocketmine\plugin\Plugin;

class HelpCommand extends CoreCommand{

    private $plugin, $server;

    public function __construct(Main $plugin, $name, $desc, $usage, array $aliases = []){
        parent::__construct($plugin, $name, $desc, $usage, $aliases);
        $this->plugin = $plugin;
        $this->server = $plugin->getServer();
    }

    public function getPlugin() : Main{
        return $this->plugin;
    }

    public function getServer(){
        return $this->server;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args){
        $sender->sendMessage($this->getPlugin()->getUtils()->getChatMessages(Prefix::DEFAULT)."No help pages currently!");
    }
}