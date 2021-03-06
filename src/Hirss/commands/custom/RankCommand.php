<?php

namespace Hirss\commands\custom;

use Hirss\commands\CoreCommand;
use Hirss\Main;
use Hirss\utils\Prefix;
use pocketmine\command\CommandSender;

class RankCommand extends CoreCommand{

    /**
     * @var Main
     */
    private $plugin;

    /**
     * RankCommand constructor.
     * @param Main $plugin
     * @param string $name
     * @param null|string $desc
     * @param $usage
     * @param array $aliases
     */
    public function __construct(Main $plugin, string $name, ?string $desc, $usage, array $aliases = []){
        parent::__construct($plugin, $name, $desc, $usage, $aliases);
        $this->plugin = $plugin;
    }

    public function getPlugin(){
        return $this->plugin;
    }


    /**
     * @param CommandSender $sender
     * @param array $args
     */
    public function onExecute(CommandSender $sender, array $args){
        $sender->sendMessage($this->getPlugin()->getUtils()->getChatMessages(Prefix::DEFAULT)."Loading...");
    }

}  