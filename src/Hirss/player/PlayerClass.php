<?php

namespace Hirss\player;

use Hirss\Main;
use Hirss\managers\GameManager;
use Hirss\utils\Prefix;
use Hirss\utils\StatsManager;
use Hirss\utils\TextToHead;
use pocketmine\event\player\cheat\PlayerIllegalMoveEvent;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\level\Location;
use pocketmine\level\Position;
use pocketmine\level\sound\ClickSound;
use pocketmine\level\sound\LaunchSound;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\MovePlayerPacket;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\network\SourceInterface;

class PlayerClass extends Player
{

    private $plugin;

    protected $interface, $ip, $port;



    public function __construct(SourceInterface $interface, string $ip, int $port){
        parent::__construct($interface, $ip, $port);
        $this->interface = $interface;
        $this->ip = $ip;
        $this->port = $port;

        $this->server = Server::getInstance();
        $plugin = Main::getInstance();
        $this->plugin = $plugin;
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
        return $this->server;
    }


    /**
     * @return string
     */
    public function getName() : string{
        return strtolower(parent::getName());
    }

    /** Returns player name in regular form
     * @return string
     */
    public function getRealName(){
        return parent::getName();
    }

    /**
     * @return float|int
     */
    public function getX(){
        return parent::getX();
    }

    /**
     * @return float|int
     */
    public function getZ(){
        return parent::getZ();
    }


    /**
     * @return mixed
     */
    public function getPassword(){
        $database = $this->getPlugin()->database->getAll();
        if($this->isRegistered()){
            return $database[strtolower($this->getName())]["password"];
        }else{
            return -1;
        }
    }

    /**
     * @return mixed
     */
    public function getLang(){
        $database = $this->getPlugin()->database->getAll();
        return $database[$this->getName()]["lang"];
    }

    /**
     * @param string $lang
     */
    public function setLang(string $lang){
        $database = $this->getPlugin()->database->getAll();
        $database[$this->getName()]["lang"] = $lang;
        $this->getPlugin()->database->setAll($database);
        $this->getPlugin()->database->save();
        $this->sendMessage($this->getPlugin()->getUtils()->getChatMessages(Prefix::DEFAULT)."Set language to ".$lang);
    }

    /**
     * @return string
     */
    public function getStats(){
        return $this->getStatsManager()->returnStats();
    }

    /**
     * Code from https://github.com/pmmp/PocketMine-MP/blob/88b3df76eb6023c0b0a42b88866a0903ea9b78ac/src/pocketmine/Player.php#L1461
     * @param int $tickDiff
     */
    
}

