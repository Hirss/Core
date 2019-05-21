<?php

namespace Hirss\commands\override;

use Hirss\Main;

use Hirss\utils\Prefix;
use pocketmine\command\CommandSender;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat;
use Hirss\commands\CoreCommand;

class MeCommand extends CoreCommand{

    private $plugin;
    private $server;

    public function __construct(Main $plugin, $name, $desc, $usage, array $aliases = []){
        parent::__construct($plugin, $name, $desc, $usage, $aliases);
        $this->plugin = $plugin;
        $this->server = $this->plugin->getServer();
    }

    public function getPlugin() : Main{
        return $this->plugin;
    }

    public function getServer(){
        return $this->server;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args){
       $sender->sendMessage(TextFormat::RED.TextFormat::BOLD."Et bah non ptdr t ki pour use cette commande ?!");
        return;
    }
}