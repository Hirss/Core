<?php

namespace Hirss\commands\custom;

use Hirss\Main;
use Hirss\player\PlayerClass;
use Hirss\commands\CoreCommand;

use pocketmine\command\CommandSender;
use pocketmine\plugin\Plugin;

class LangCommand extends CoreCommand{

    private $plugin;
    private $server;

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
        if(isset($args[0])){
            if(isset(Main::$langs[$args[0]])){
                if(is_string($args[0])){
                    if($sender instanceof PlayerClass){
                        $sender->setLang($args[0]);
                        $sender->sendMessage("Set language preference to ".Main::$langs[$args[0]]);
                    }
                }
            }
        }
    }
}