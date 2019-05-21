<?php

namespace Hirss\commands\override;

use Hirss\Main;

use Hirss\player\PlayerClass;
use Hirss\utils\Prefix;
use pocketmine\command\CommandSender;
use Hirss\commands\CoreCommand;
use pocketmine\plugin\Plugin;

class TellCommand extends CoreCommand{

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
        if(isset($args[0])){
            $player = $this->getServer()->getPlayer($args[0]);
            if($player === null){
                $sender->sendMessage($this->getPlugin()->getUtils()->getChatMessages(Prefix::PLAYER_NOT_ONLINE));
            }else{
                if($sender instanceof PlayerClass and $player instanceof PlayerClass){
                    $message = implode(" ", $args[1]);
                    $player->sendMessage($this->getPlugin()->getUtils()->getChatMessages(Prefix::DEFAULT).$sender->getRealName()."->You ".$message);
                    $sender->sendMessage($this->getPlugin()->getUtils()->getChatMessages(Prefix::DEFAULT)."You messaged ".$player->getRealName().": ".$message);
                }
            }
        }
    }

}
