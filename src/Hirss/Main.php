<?php

declare(strict_types=1);

namespace Hirss;
 
 use Hirss\commands\AfkCommand;
use Hirss\commands\ClearInventoryCommand;
use Hirss\commands\FeedCommand;
use Hirss\commands\FlyCommand;
use Hirss\commands\FreezeCommand;
use Hirss\commands\GamemodeCreativeCommand;
use Hirss\commands\GamemodeSpectatorCommand;
use Hirss\commands\GamemodeSurvivalCommand;
use Hirss\commands\GodCommand;
use Hirss\commands\HealCommand;
use Hirss\commands\KickAllCommand;
use Hirss\commands\PingCommand;
use Hirss\commands\SpawnCommand;
use Hirss\commands\TpAllCommand;
use Hirss\commands\VanishCommand;
use Hirss\commands\WildCommand;
use Hirss\commands\XYZCommand;
use Hirss\commands\PartyCommand;
use Hirss\commands\PartyCommandMap;

use Hirss\commands\presets\AcceptCommand;
use Hirss\commands\presets\ChatCommand;
use Hirss\commands\presets\CreateCommand;
use Hirss\commands\presets\DisbandCommand;
use Hirss\commands\presets\InviteCommand;
use Hirss\commands\presets\JoinCommand;
use Hirss\commands\presets\KickCommand;
use Hirss\commands\presets\LeaveCommand;
use Hirss\commands\presets\ListCommand;
use Hirss\commands\presets\LockCommand;
use Hirss\commands\presets\PromoteCommand;

use Hirss\events\party\PartyCreateEvent;
use Hirss\events\party\PartyDisbandEvent;
use Hirss\events\party\PartyEvent;
use Hirss\events\party\PartyPromoteEvent;
use Hirss\parties\party\PartyManager;
use Hirss\parties\session\SessionManager;
use Hirss\tasks\ClearLaggTask;
use Hirss\tasks\PlayerHealTask;
use Hirss\events\EventsListener;
use Hirss\utils\MySQLProvider;
use pocketmine\scheduler\Task;
use pocketmine\utils\Config;
use Hirss\commands\CoreCommand;
use Hirss\utils\Permissions;
use Hirss\utils\Prefix;
use Hirss\player\PlayerClass;
use Hirss\utils\Utils;
use pocketmine\entity\Entity;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\command\CommandExecutor;
use pocketmine\item\Item;
use jojoe77777\FormAPI;
use pocketmine\Player;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\math\Vector3;
use pocketmine\event\player\PlayerItemConsumeEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemHeldEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\entity\ProjectileHitEvent;
use pocketmine\event\entity\ProjectileLaunchEvent;
use pocketmine\event\Listeners;
use pocketmine\utils\TextFormat;
use pocketmine\level\Level;
use pocketmine\network\mcpe\protocol\LoginPacket;
use pocketmine\event\server\DataPacketReceiveEvent;

class Main extends PluginBase implements Listener
{
	public const PREFIX = TextFormat::RED . TextFormat::BOLD . "Fightcube > " . TextFormat::RESET;
	
    public static $badwords = ["cunt", "whore", "bitch", "nigger", "fuck", 'shit', 'ass', 'slut', 'faggot', 'fag', 'motherfucker', 'dick', 'pussy', 'penis', 'fdp', 'pd', 'ntm', 'fake dev'];
    private static $obj = null;


  public $nicks = ["AwarZip", "SsriH", "LedA", "InstanceOf", "BecauseYou", "FightPlayZ", "BreadZ", "ImFag", "xSqd", "BoyGotLove", "Kimkardachiant", "Secure-Heberg", "Azox", "Dinner", "xNeon", "KissS", "DragonPlayz", "Newbie", "GirlGotLove", "SingLee", "PopCornHorns"];
  
    public static $langs = [];
    /* @var Task*/
    public $tasks = [];
    public $settings;
    /* @var Config*/
    public $database;
    /* @var Utils*/
    public $utils;
    public $sql;

public $water = array("WaterParticles");
    public $fire = array("FireParticles");
    public $heart = array("HeartParticles");
    public $smoke = array("SmokeParticles");
    //EnderPearl
    /**@var Item*/
    private $item;
    /**@var int*/
    public $damage = 0;
    private static $instance = null;
    private $commandMap;
    private $partyManager;
    private $sessionManager;
    public $API;
    public $prefix = "§7[§2Friends§7]§r ";
    
		public function onLoad() {
        self::$instance = $this;
 	  }

    public function onEnable() {
        @mkdir($this->getDataFolder());
        $this->saveResource("config.yml");
        API::$instance = $this;
		API::setMotd(str_replace("&", "§", strval($this->getConfig()->get("motd"))));
		@mkdir($this->getDataFolder());
		$this->saveDefaultConfig();
		$this->eco = $this->getServer()->getPluginManager()->getPlugin("EconomyAPI");
		$this->getServer()->getCommandMap()->registerAll("Hirss", [
		    new PartyCommandMap($this), 
			new ClearInventoryCommand($this),
			new FeedCommand($this),
			new FlyCommand($this),
			new FreezeCommand($this),
			new GamemodeCreativeCommand($this),
			new GamemodeSpectatorCommand($this),
			new GamemodeSurvivalCommand($this),
			new HealCommand($this), 
			new WildCommand($this),
			new VanishCommand($this),
			new SpawnCommand($this),
			new XYZCommand($this),
			new GodCommand($this),
			new AfkCommand($this),
			new KickAllCommand($this),
			new TpAllCommand($this),
			new PingCommand($this)
		]);
		 $this->config = new Config($this->getDataFolder().'config.yml', Config::YAML);
		$this->getScheduler()->scheduleRepeatingTask(new ClearLaggTask($this), 120 * 20);
		$this->getScheduler()->scheduleRepeatingTask(new PlayerHealTask($this), 10);
		$this->sessionManager = new SessionManager($this);
        $this->partyManager = new PartyManager($this);
        $this->settings = new Config($this->getDataFolder(). "settings.yml");
        $this->getScheduler()->scheduleRepeatingTask(new Particles($this), 5);
		$this->getServer()->getPluginManager()->registerEvents(new CosmeticMenu($this), $this);
		$this->getServer()->getPluginManager()->registerEvents(new Navigator($this), $this);
		$this->getServer()->getPluginManager()->registerEvents(new EventsListener($this), $this);
		$this->getServer()->getPluginManager()->registerEvents(new StackEvent($this), $this);
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
        $this->utils = new Utils($this);
        CoreCommand::registerAll($this, $this->getServer()->getCommandMap());
        if($this->settings->get("use_sql")){
            $this->sql = new MySQLProvider($this);
            $this->sql->process();
            $this->getLogger()->info($this->getUtils()->getChatMessages(Prefix::DEFAULT_BAD)."MySQLProvider enabled! It is recommended to not use MySQL at this plugins current state");
        }else{
            $this->getLogger()->info($this->getUtils()->getChatMessages(Prefix::DEFAULT_BAD)."MySQLProvider not enabled! Using YAML");
        }

        $this->getLogger()->info($this->getUtils()->getChatMessages(Prefix::DEFAULT)."Enabled!");

    }
    public function onMove(PlayerMoveEvent $event){
        $player = $event->getPlayer();
        $block = $player->getLevel()->getBlock($player->subtract(0, -1 , 0));
        if ($block->getId() === Item::WATER) {
            $player->setDataFlag(Entity::DATA_FLAGS, Entity::DATA_FLAG_SWIMMING, true);
        } else {
            $player->setDataFlag(Entity::DATA_FLAGS, Entity::DATA_FLAG_SWIMMING, false);
        }
    }
	    public static function getInstance() {
        return self::$instance;
    }
    public function getCommandMap(): PartyCommandMap {
        return $this->commandMap;
    }
    public function getPartyManager(): PartyManager {
        return $this->partyManager;
    }
    public function getSessionManager(): SessionManager {
        return $this->sessionManager;
    }
	
	public function openSizeForm($player){
		$api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
		$form = $api->createSimpleForm(function (Player $player, int $data = null){
			    $result = $data;
			    if($result === null){
				      return true;
				}
				switch($result){
					case "0";
					         $player->setScale("0.3");
				             $player->sendMessage("§eYour Size change to MINI!");
					break;
					
					case "1";
					         $player->setScale("1.0");
				             $player->sendMessage("§eYour Size change to Normal!");
					break;
					
					case "2";
					         $player->setScale("1.70");
				             $player->sendMessage("§eYour Size change to GRAND!");
					break;
					
					case "3";
					         $player->setScale("3.0");
				             $player->sendMessage("§eYour Size change to MEGAGRAND!");
					break;
					}
					
			
			});
			$form->setTitle("§6SizeUI");
			$form->setContent(TextFormat::GREEN."Change your Size!:");
			$form->addButton("Mini");
			$form->addButton("Normal");
			$form->addButton("Grand");
			$form->addButton("MegaGrand");
			$form->sendToPlayer($player);
			return $form;
			
if($cmd->getName() == "friends"){
if($sender instanceof Player){
$playerfile = new Config($this->getDataFolder().$sender->getName().".yml", Config::YAML);
if(empty($args[0])){
$sender->sendMessage($this->prefix."/friend <invite | list | remove | block>");
}else{
if($args[0] == "invite"){
if(empty($args[1])){
$sender->sendMessage($this->prefix."/friends invite <player>");
}else{
if(file_exists($this->getDataFolder().$args[1].".yml")){
$vplayerfile = new Config($this->getDataFolder().$args[1].".yml", Config::YAML);
if($vplayerfile->get("blocked") == false){
$einladungen = $vplayerfile->get("Invitations");
$einladungen[] = $sender->getName();
$vplayerfile->set("Invitations", $einladungen);
$vplayerfile->save();
$sender->sendMessage($this->prefix."§aFriends invitations was sent to ".$args[1]);
$v = $this->getServer()->getPlayerExact($args[1]);
if(!$v == null){
$v->sendMessage("§a".$sender->getName()." send you invitation for friendship\n   §l[/friends accept ".$sender->getName()."]    §c[/friends deny ".$sender->getName()."]");
}
}else{
$sender->sendMessage($this->prefix."§cthis player did not accept your request!");
}
}else{
$sender->sendMessage($this->prefix."§4Player is not online");
}
}
}
if($args[0] == "accept"){
if(empty($args[1])){
$sender->sendMessage($this->prefix."/friends accept <player>");
}else{
if(file_exists($this->getDataFolder().$args[1].".yml")){
$vplayerfile = new Config($this->getDataFolder().$args[1].".yml", Config::YAML);
if(in_array($args[1], $playerfile->get("Invitations"))){
$old = $playerfile->get("Invitations");
unset($old[array_search($args[1], $old)]);
$playerfile->set("Invitations", $old);
$newfriend = $playerfile->get("Friend");
$newfriend[] = $args[1];
$playerfile->set("Friend", $newfriend);
$playerfile->save();
$vplayerfile = new Config($this->getDataFolder().$args[1].".yml", Config::YAML);
$newfriend = $vplayerfile->get("Friend");
$newfriend[] = $sender->getName();
$vplayerfile->set("Friend", $newfriend);
$vplayerfile->save();
if(!$this->getServer()->getPlayerExact($args[1]) == null){
$this->getServer()->getPlayerExact($args[1])->sendMessage($this->prefix."§a".$sender->getName()." is your friend now");
}
$sender->sendMessage($this->prefix."§a".$args[1]." is your friend now");
}else{
$sender->sendMessage($this->prefix."§cThis player did not send you an invitation!");
}
}else{
$sender->sendMessage($this>prefix."§4This player does not exist");
}
}
}

if($args[0] == "deny"){
if(empty($args[1])){
$sender->sendMessage($this->prefix."/friends deny <player>");
}else{
if(file_exists($this->getDataFolder().$args[1].".yml")){
$vplayerfile = new Config($this->getDataFolder().$args[1].".yml", Config::YAML);
if(in_array($args[1], $playerfile->get("Invitations"))){
$old = $playerfile->get("Invitations");
unset($old[array_search($args[1], $old)]);
$playerfile->set("Invitations", $old);
$playerfile->save();
$sender->sendMessage($this->prefix."§aThe invitation from".$args[1]." was declined");
}else{
$sender->senMessage($this->prefix."§cThis player did not send you an invitation!");
}
}else{
$sender->sendMessage($this->prefix."§4This player does not exist");
}
}
}

if($args[0] == "remove"){
if(empty($args[1])){
$sender->sendMessage($this->prefix."/friends remove <player>");
}else{
if(file_exists($this->getDataFolder().$args[1].".yml")){
$vplayerfile = new Config($this->getDataFolder().$args[1].".yml", Config::YAML);
if(in_array($args[1], $playerfile->get("Friend"))){
$old = $playerfile->get("Friend");
unset($old[array_search($args[1], $old)]);
$playerfile->set("Friend", $old);
$playerfile->save();
$vplayerfile = new Config($this->getDataFolder().$args[1].".yml", Config::YAML);
$old = $vplayerfile->get("Friend");
unset($old[array_search($sender->getName(), $old)]);
$vplayerfile->set("Friend", $old);
$vplayerfile->save();
$sender->sendMessage($this->prefix."§a".$args[1]." is not your friend anymore");
}else{
$sender->senMessage($this->prefix."§cThis player is not your friend!");
}
}else{
$sender->sendMessage($this->prefix."§4This player does not exist");
}
}
}

if($args[0] == "list"){
if(empty($playerfile->get("Friend"))){
$sender->sendMessage($this->prefix."§cYou have no friends");
}else{
$sender->sendMessage("§eYour friends:");
foreach($playerfile->get("Friend") as $f){
if($this->getServer()->getPlayerExact($f) == null){
$sender->sendMessage("§e".$f."§7(§coffline§7)");
}else{
$sender->sendMessage("§e".$f."§7(§aonline§7)");
}
}
}
}
if($args[0] == "block"){
if($playerfile->get("blocked") === false){
$playerfile->set("blocked", true);
$playerfile->save();
$sender->sendMessage($this->prefix."§aYou will no longer receive friend requests");
}else{
$sender->sendMessage($this->prefix."§aYou will now receive friend requests again");
$playerfile->set("blocked", false);
$playerfile->save();
}
}



}
}else{
$this->getLogger()->info($this->prefix."§4The console has no friends lol");
}
}
return true;
}
       
    public function getUtils(){
        return $this->utils;
    }

    public function getMySQL() : MySQLProvider{
        return $this->sql;
    }
  
    public function onQuit(PlayerQuitEvent $event)
    {

        $player = $event->getPlayer();
        $name = $player->getName();
        $event->setQuitMessage("§7[§c-§7] §c" . $name);
        
$playerfile = new Config($this->getDataFolder().$name.".yml", Config::YAML);
if(!empty($playerfile->get("Friend"))){
foreach($playerfile->get("Friend") as $f){
$v = $this->getServer()->getPlayerExact($f);
if(!$v == null){
$v->sendMessage($this->prefix."§a".$player->getName()." is now offline");
         }
        }
      }
    }
        public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args):bool
    {
        switch($cmd->getName()){
        	case "shop":
        if(!$sender instanceof Player){
                $sender->sendMessage("§cThis command can't be used here.");
                return true;
        }
        $api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
        $form = $api->createSimpleForm(function (Player $sender, $data){
            $result = $data;
            if ($result == null) {
            }
            switch ($result) {
                    case 0:
                        break;
                    case 1:
                    $this->vip($sender);
                        break;
            }
        });
        $form->setTitle("§lRanks");
        $form->setContent("§7Select Rank You Want To Buy.");
        $form->addButton("§l§cExit", 0);
        $form->addButton("§lVIP", 1);
        $form->sendToPlayer($sender);
        return true;
        case "wtp":
				if(!(isset($args[0]))) {
                    $sender->sendMessage("Use /wtp <world name>");
                    return false;
                } else {
				    if($sender instanceof Player){
                        if ($this->getServer()->loadLevel($args[0]) != false){
                            $sender->teleport($this->getServer()->getLevelByName($args[0])->getSafeSpawn(), null, null);
                        } else {
                            if($this->getServer()->getLevelByName($args[0]) == false){
                                $sender->sendMessage("That world doesn't exist.");
                                return false;
                            } else {
                                $this->getServer()->loadLevel($args[0]);
                                $sender->sendMessage($args[0] ." world is being loaded. Please try again soon.");
                            }
                        }
                    }
                }
                break;
            case "create":
                if(!(isset($args[0]))){
                    $sender->sendMessage(TextFormat::GREEN."Use /create <world name>");
                    return false;
                } else {
                    if($this->getServer()->getLevelByName($args[0])){
                        $sender->sendMessage(TextFormat::GREEN."A world with that name already exists.");
                    }else {
                        $this->getServer()->generateLevel($args[0],null,"FLAT",["preset" => "2;0;1"]);
                        $sender->sendMessage($args[0]." has been created.");
                        }
                    }
                    break;
                }
		return true;
    }
    public function vip($sender){
        $api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
        $form = $api->createModalForm(function (Player $sender, $data){
            $result = $data;
            if ($result == null) {
            }
            switch ($result) {
            	
                    case 1:
            $money = $this->eco->myMoney($sender);
            $fly = $this->getConfig()->get("vip.cost");
            if($money >= $fly){

               $this->eco->reduceMoney($sender, $fly);
               $this->getServer()->dispatchCommand(new ConsoleCommandSender(), "setgroup " . $sender->getName() . " vip");
               $sender->sendMessage("§aYou successfully purchased VIP rank!");
              return true;
            }else{
               $sender->sendMessage("§cYou don't have enough money to buy VIP rank");
            }
                        break;
                        
                    case 2:
               $sender->sendMessage("§bYou cancelled buying VIP rank");
                        break;
            }
        });
        $form->setTitle("§lVIP");
        $form->setContent("§eAre you sure you want to buy this rank?");
        $form->setButton1("§l§aConfirm", 1);
        $form->setButton2("§l§cCancel", 2);
        $form->sendToPlayer($sender);
    }

    public function onPlace(BlockPlaceEvent $ev)
    {
		$ev->setCancelled(false);
    }

    public function Hunger(PlayerExhaustEvent $ev)
    {
		$ev->setCancelled(true);
    }

    public function ItemMove(PlayerDropItemEvent $ev)
    {
		$ev->setCancelled(true);
    }

    public function onConsume(PlayerItemConsumeEvent $ev)
    {
		$ev->setCancelled(true);
    }

    public function Main(Player $player)
    {
        $player->getInventory()->clearAll();
        $player->getInventory()->setItem(6, Item::get(288)->setCustomName(TextFormat::BLUE . "Fly"));
        $player->getInventory()->setItem(2, Item::get(289)->setCustomName(TextFormat::YELLOW . "Hide ".TextFormat::GREEN."Players"));
		$player->getInventory()->setItem(0, Item::get(345)->setCustomName(TextFormat::AQUA . "Navigator"));
		// Party UI Soon $player->getInventory()->setItem(7, Item::get(388)->setCustomName(TextFormat::RED . "Party"));
		$player->getInventory()->setItem(8, Item::get(356)->setCustomName(TextFormat::RED . "Options"));
		$player->getInventory()->setItem(5, Item::get(266)->setCustomName(TextFormat::GOLD . "Shop"));
    }

    public function onInteract(PlayerInteractEvent $event)
    {
        $player = $event->getPlayer();
        $item = $player->getInventory()->getItemInHand();
                  $itemname = $item->getCustomName();
                  
                      if ($itemname === "§6Shop"){
        $player->getServer()->dispatchCommand($player, "shop");
        }
        if ($item->getCustomName() == TextFormat::AQUA . "Navigator") {
            $api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
            $form = $api->createSimpleForm(function (Player $sender, $data) {
                $result = $data[0];

                if ($result === null) {
                    return true;
                }
                switch ($result) {
                case 0:
                    $command = "transferserver play.asudia-pe.tk:19134";
					$this->getServer()->getCommandMap()->dispatch($sender, $command);
				break;
                case 1:
                    $command = "transferserver ";
					$this->getServer()->getCommandMap()->dispatch($sender, $command);
                break;
                case 2:
                    $command = "transferserver";
					$this->getServer()->getCommandMap()->dispatch($sender, $command);
				break;
                case 3:
                    $command = "transferserver";
					$this->getServer()->getCommandMap()->dispatch($sender, $command);
				break;
              case 4:
                    $command = "transferserver";
					$this->getServer()->getCommandMap()->dispatch($sender, $command);
				break;
            }
			});
			$form->setTitle("§5NavigatorUI");
			$form->setContent("§7Choose a mini game");
			$form->addButton("§cHikabrain", 0);
			$form->addButton("§acRush", 1);
			$form->addButton("§cSkyWars", 2);
			$form->addButton("§cFaction", 3);
     $form->addButton("§cSkyBlock", 4);
			$form->addButton("§cQuitter", 5);
			$form->sendToPlayer($player);
			}
        if ($item->getCustomName() == TextFormat::BLUE . "Fly") {
            $api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
            $form = $api->createSimpleForm(function (Player $sender, $data){
                $result = $data;
                if($result != null) {
                }
                switch ($result) {
                    case 0;
                        $sender->setAllowFlight(true);
                        $sender->sendMessage("§aFly -> ON!");
                        break;
                    case 1;
                        $sender->setAllowFlight(false);
                        $sender->sendMessage("§cFly -> OFF");
                        break;
                    case 2;
                }
            });
            $form->setTitle("§6Fly UI");
            $form->setContent("§b§oFly UI ");
            $form->addbutton("§l§2ON", 0);
            $form->addbutton("§l§cOFF", 1);
            $form->addButton("§4§lEXIT", 2);
            $form->sendToPlayer($player);
        }

        if ($item->getCustomName() == TextFormat::RED . "Options") {
            $api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
            $form = $api->createSimpleForm(function (Player $sender, $data){
                $result = $data;
                if($result != null) {
                }
                switch ($result) {
                    case 0;
                                        $command = "";
					$this->getServer()->getCommandMap()->dispatch($sender, $command);
                        break;
                    case 1;
                        break;
                    case 2;
                }
            });
            $form->setTitle("§c§lOptions");
            $form->setContent("§b§oAdd Content ");
            $form->addbutton("§l§cAdd Option", 0);
            $form->addbutton("§l§cAdd Option", 1);
            $form->addButton("§4§lEXIT", 2);
            $form->sendToPlayer($player);
        }
        
        if ($item->getName() === TextFormat::YELLOW . "Hide ".TextFormat::GREEN."Players") {
            $player->getInventory()->remove(Item::get(289)->setCustomName(TextFormat::YELLOW . "Hide ".TextFormat::GREEN."Players"));
            $player->getInventory()->setItem(2, Item::get(348)->setCustomName(TextFormat::YELLOW . "Show ".TextFormat::GREEN."Players"));
            $player->sendMessage(TextFormat::RED . "Disabled Player Visibility!");
            $this->hideall[] = $player;
            foreach ($this->getServer()->getOnlinePlayers() as $p2) {
                $player->hideplayer($p2);
            }

        } elseif ($item->getName() === TextFormat::YELLOW . "Show ".TextFormat::GREEN."Players"){
            $player->getInventory()->remove(Item::get(348)->setCustomName(TextFormat::YELLOW . "Show ".TextFormat::GREEN."Players"));
            $player->getInventory()->setItem(2, Item::get(289)->setCustomName(TextFormat::YELLOW . "Hide ".TextFormat::GREEN."Players"));
            $player->sendMessage(TextFormat::GREEN . "Enabled Player Visibility!");
            unset($this->hideall[array_search($player, $this->hideall)]);
            foreach ($this->getServer()->getOnlinePlayers() as $p2) {
                $player->showplayer($p2);
            }
        } 
    }
    
   public function onJoin(PlayerJoinEvent $event){
	$player = $event->getPlayer();
	$inv = $player->getInventory();
        $name = $player->getName();
        $this->Main($player);
        $event->setJoinMessage("§7[§9+§7] §9" . $name);
	$itemhand = Item::get(347, 0, 1);
	$inv->setItem(1, $itemhand);
	
	if(!file_exists($this->getDataFolder().$name.".yml")){
$playerfile = new Config($this->getDataFolder().$name.".yml", Config::YAML);
$playerfile->set("Friend", array());
$playerfile->set("Invitations", array());
$playerfile->set("blocked", false);
$playerfile->save();
}else{
$playerfile = new Config($this->getDataFolder().$name.".yml", Config::YAML);
if(!empty($playerfile->get("Invitations"))){
foreach($playerfile->get("Invitations") as $e){
$player->sendMessage($this->prefix."§e".$e." has sent you a friend request");
}
}

if(!empty($playerfile->get("Friend"))){
foreach($playerfile->get("Friend") as $f){
$v = $this->getServer()->getPlayerExact($f);
if(!$v == null){
$v->sendMessage($this->prefix."§a".$player->getName()." is now online");
             }
           }
        }
      }
    }
    public function onChat(PlayerChatEvent $event){
$player = $event->getPlayer();
$msg = $event->getMessage();
$playerfile = new Config($this->getDataFolder().$player->getName().".yml", Config::YAML);
$words = explode(" ", $msg);
if(in_array(str_replace("@", "", $words[0]), $playerfile->get("Friend"))){
$f = $this->getServer()->getPlayerExact(str_replace("@", "", $words[0]));
if(!$f == null){
$f->sendMessage($this->prefix." §7[§e".str_replace("@", "", $words[0])."§7] §l>>§r ".str_replace($words[0], "", $msg));
$player->sendMessage($this->prefix." §7[§e".str_replace("@", "", $words[0])."§7] §l>>§r ".str_replace($words[0], "", $msg));
}else{
$player->sendMessage($this->prefix."§c".str_replace("@", "", $words[0])." is not online!");
}
$event->setCancelled();
}
}
}
            