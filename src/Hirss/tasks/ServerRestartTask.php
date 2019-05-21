<?php

namespace Hirss\tasks;


use Hirss\Main;
use Hirss\player\PlayerClass;
use Hirss\utils\Prefix;
use pocketmine\scheduler\Task;
use pocketmine\utils\TextFormat;

class ServerRestartTask extends Task{

    private $plugin, $time;

    public function __construct(Main $plugin){
        $this->plugin = $plugin;
        $this->time = $plugin->settings->get("Server_restart_time");
    }

    /**
     * @return Main
     */
    public function getPlugin(){
        return $this->plugin;
    }

    /**
     * @return \pocketmine\Server
     */
    public function getServer(){
        return $this->getPlugin()->getServer();
    }

    /**
     * @return int
     */
    public function getTime(){
        return $this->time;
    }

    public function reduceTime(){
        $time = $this->getTime();
        $this->time = ($time - 1);
    }

    public function onRun(int $currentTick){
        $this->reduceTime();

        foreach($this->getServer()->getOnlinePlayers() as $player){
            if($player instanceof PlayerClass){
                if(!$player->isQueued() && $player->inGame !== true){
                    $player->sendTip(TextFormat::AQUA."Server restarting in ".TextFormat::WHITE.gmdate("i:s", $this->getTime()));
                }
            }
        }

        if($this->getTime() === 60){
            $this->getServer()->broadcastMessage($this->getPlugin()->getUtils()->getChatMessages(Prefix::DEFAULT)."Server restarting in 1 minute!");
        }

        if($this->getTime() === 0){
            sleep(1);
            $this->getServer()->forceShutdown();
        }
    }
}