<?php

namespace Hirss\commands\custom;

use Hirss\commands\CoreCommand;
use Hirss\Main;
use Hirss\player\PlayerClass;
use Hirss\utils\Prefix;
use pocketmine\command\CommandSender;
use pocketmine\plugin\Plugin;

class LobbyCommand extends CoreCommand{

    private $plugin, $server;

    public function __construct(Main $plugin, $name, $desc, $usage, array $aliases = []){
        parent::__construct($plugin, $name, $desc, $usage, $aliases);
        $this->plugin = $plugin;
        $this->server = $plugin->getServer();
    }

    public function getPlugin(): Main{
        return $this->getPlugin();
    }

    public function getServer(){
        return $this->server;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args){
        if($sender instanceof PlayerClass){
            $level = $this->getServer()->getDefaultLevel()->getSpawnLocation();
            $sender->teleport($level);
            $sender->removeAllEffects();
            $sender->setMaxHealth(20);
            $sender->setHealth(20);
        }
    }
}
