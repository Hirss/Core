<?php

declare(strict_types=1);

namespace Hirss\events;

use Hirss\Main;

use Hirss\managers\GameManager;
use Hirss\npc\HumanNPC;
use Hirss\player\PlayerClass;
use Hirss\utils\Permissions;
use Hirss\utils\Prefix;
use Hirss\utils\Utils;
use Hirss\API;
use Hirss\commands\AfkCommand;
use Hirss\commands\FreezeCommand;
use Hirss\commands\GodCommand;
use Hirss\commands\MuteCommand;
use Hirss\commands\WildCommand;
use Hirss\tasks\JoinTitleTask;
use Hirss\libs\JackMD\ScoreFactory\ScoreFactory;

use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\block\Block;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerCreationEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\item\Item;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class EventsListener implements Listener{

    private $plugin;

    /**
     * EventsListener constructor.
     * @param Main $plugin
     */
     public function __construct(Main $plugin){
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
        return $this->getPlugin()->getServer();
    }

    /**
     * @param PlayerCreationEvent $ev
     * @priority HIGHEST
     */
    public function setPlayerClass(PlayerCreationEvent $ev){
        $ev->setPlayerClass(PlayerClass::class);
    }

    /**
     * @param PlayerInteractEvent $ev
     */
     public function onQuit(PlayerQuitEvent $event){
		$player = $event->getPlayer();
		ScoreFactory::removeScore($player);
	}
     public function onMove(PlayerMoveEvent $event) : void{
		$player = $event->getPlayer();
		if(in_array($player->getName(), FreezeCommand::$initFreeze)) $event->setCancelled(true);
		if(in_array($player->getName(), AfkCommand::$afk)){
			unset(AfkCommand::$afk[array_search($player->getName(), AfkCommand::$afk)]);
			$player->sendMessage(Main::PREFIX . TextFormat::GREEN . "You are no longer in afk mode");
			API::getMainInstance()->getServer()->broadcastMessage(TextFormat::YELLOW . $player->getName() . " is no longer AFK");
		}
	}
	//public function onPlayerLogin(PlayerLoginEvent $event) : void{
		//$event->getPlayer()->teleport(API::getMainInstance()->getServer()->getDefaultLevel()->getSafeSpawn());
	//}
	public function onEntityDamage(EntityDamageEvent $event) : void{
		$entity = $event->getEntity();
		if($entity instanceof Player){
			if(in_array($entity->getName(), WildCommand::$initWild)){
				if($event->getCause() === EntityDamageEvent::CAUSE_FALL){
					$event->setCancelled(true);
					unset(WildCommand::$initWild[array_search($entity->getName(), WildCommand::$initWild)]);
				}
			}elseif(in_array($entity->getName(), GodCommand::$god)){
				$event->setCancelled(true);
			}
			if($entity->getPosition()->getY() < 0){
				if(API::getMainInstance()->getConfig()->get("novoid") === "on"){
					$event->setCancelled(true);
					$entity->teleport(API::getMainInstance()->getServer()->getDefaultLevel()->getSafeSpawn());
					$entity->sendMessage(Main::PREFIX . TextFormat::GREEN . "You were teleported out of the void");
				}
			}
		}
	}

    public function onInteract(PlayerInteractEvent $ev){
        $player = $ev->getPlayer();
        $username = $player->getName();
        $block = $ev->getBlock();
        if(isset($this->getPlugin()->isSetting[$player->getName()])){
            switch($this->getPlugin()->isSetting[$username]["int"]){
                case 0:
                    $this->pos1 = ["x" => $block->x,
                        "y" => $block->y,
                        "z" => $block->z,
                        "level" => $player->getLevel()->getName()];
                    $this->getPlugin()->isSetting[$username]["int"]++;
                    $player->sendMessage($this->getPlugin()->getUtils()->getChatMessages(Prefix::DEFAULT)."Position one set please select the next!");
                    break;
                case 1:
                    $this->pos2 = ["x" => $block->x,
                        "y" => $block->y,
                        "z" => $block->z,
                        "level" => $player->getLevel()->getName()];
                    $match_number = $this->getPlugin()->newMatch($this->pos1, $this->pos2, $this->getPlugin()->isSetting[$username]["type"]);
                    $player->sendMessage($this->getPlugin()->getUtils()->getChatMessages(Prefix::DEFAULT)."Done! All positions set for match #".$match_number." for ".$this->getPlugin()->isSetting[$username]["type"]."!");
                    unset($this->getPlugin()->isSetting[$username]);
                    break;
            }
        }
        if($ev->getAction() === PlayerInteractEvent::RIGHT_CLICK_BLOCK){
            if($player->getInventory()->getItemInHand()->getId() === Item::MUSHROOM_STEW){
                if($player->getHealth() === $player->getMaxHealth()){
                    return;
                }else{
                    $player->getInventory()->removeItem(Item::get(Item::MUSHROOM_STEW,0,1));
                    $player->setHealth(($player->getHealth() + 1.5));
                }
            }
        }
    }

public function onJoin(PlayerJoinEvent $event) : void{
		$player = $event->getPlayer();
		API::getMainInstance()->getScheduler()->scheduleDelayedTask(new JoinTitleTask(API::getMainInstance(), $player), 30);
		$player->sendMessage(strval(API::getMainInstance()->getConfig()->get("join-message")));
	}

	public function onExhaust(PlayerExhaustEvent $event) : void{
		if(API::getMainInstance()->getConfig()->get("hunger-disabler") === "on") $event->setCancelled(true);
}
    /**
     * @param PlayerChatEvent $ev
     */
     
    
}