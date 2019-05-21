<?php

namespace Hirss\commands;

use Hirss\commands\custom\LobbyCommand;
use Hirss\commands\custom\NPCCommand;
use Hirss\commands\custom\PermissionCommand;
use Hirss\commands\custom\TestCommand;
use pocketmine\command\CommandMap;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use Hirss\commands\custom\LangCommand;
use Hirss\commands\override\MeCommand;
use Hirss\commands\override\TellCommand;
use Hirss\commands\override\HelpCommand;
use pocketmine\command\PluginIdentifiableCommand;
use Hirss\Main;
use pocketmine\plugin\Plugin;

class CoreCommand extends Command{

    private $plugin;

    /**
     * HirssCommand constructor.
     * @param Main $plugin
     * @param string $name
     * @param null|string $desc
     * @param array|\string[] $usage
     * @param array $aliases
     */
    public function __construct(Main $plugin, $name, $desc, $usage, $aliases = []){
        parent::__construct($name, $desc, $usage, (array)$aliases);
        $this->plugin = $plugin;
    }


    public function getPlugin(){
        return $this->plugin;
    }

    /**
     * @param Main $main
     * @param CommandMap $map
     */
    public static function registerAll(Main $main, CommandMap $map){
        $cmds = ["tell","help","me","?","msg"];
        foreach($cmds as $cmd){
            self::unregisterCommand($map, $cmd);
        }
        $map->registerAll("c", [
            new TellCommand($main, "tell", "Send a player a private message", null),
            new LangCommand($main, "lang", "Change your language preference!", null),
            new MeCommand($main, "me", "Shout yourself out!", null),
            new HelpCommand($main, "help", "See the list of commands!", null),
            new LobbyCommand($main, "lobby", "Go back to the lobby!", "/spawn")]);
    }

    /**
     * @param CommandMap $map
     * @param $name
     * @return bool
     */
    public static function unregisterCommand(CommandMap $map, $name){
        $cmd = $map->getCommand($name);
        if($cmd instanceof Command){
            $cmd->setLabel($name . "_disabled");
            $cmd->unregister($map);
            return true;
        }
        return false;
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     * @return bool
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args){
        if(parent::testPermission($sender) === false){
            return false;
        }else{
            $result = $this->onExecute($sender, $args);
            if(is_string(strtolower($result))){
                $sender->sendMessage($result);
            }
            return true;
        }
    }

    /**
     * @param CommandSender $sender
     * @param array $args
     * @return bool
     */
    public function onExecute(CommandSender $sender, array $args){
        if(parent::testPermission($sender) === false){
            return false;
        }
        return true;
    }
}