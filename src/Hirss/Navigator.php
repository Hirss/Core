<?php

namespace Hirss;

use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use jojoe77777\FormAPI;
use pocketmine\utils\TextFormat as TF;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\item\Item;
use pocketmine\event\player\{PlayerInteractEvent, PlayerDropItemEvent, PlayerItemHeldEvent};
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\Listener;
use pocketmine\inventory\Inventory;

class Navigator implements Listener {
	private $plugin;
public function __construct($plugin) {
$this->plugin = $plugin;
}
     public function onCommando(CommandSender $sender, Command $cmd, string $label, array $args): bool{
        switch($cmd->getName()){ 	
        case "compass":
			if(!($sender instanceof Player)){
			}
		
			$api = $this->plugin->getServer()->getPluginManager()->getPlugin("FormAPI");
			$form = $api->createSimpleForm(function (Player $sender, $data){
            $result = $data;
            if ($result == null) {
            }
            switch($result) {
                case 0:
                    $command = "rush";
					$this->plugin->getServer()->getCommandMap()->dispatch($sender, $command);
				break;
                case 1:
                    $command = "hikabrain";
					$this->plugin->getServer()->getCommandMap()->dispatch($sender, $command);
                break;
                case 2:
                    $command = "blitz";
					$this->plugin->getServer()->getCommandMap()->dispatch($sender, $command);
				break;
              case 3:
                    $command = "skywars";
					$this->plugin->getServer()->getCommandMap()->dispatch($sender, $command);
				break;
              case 4:
				break;
            }
			});
			$form->setTitle("§5Menu principal des mini jeu");
			$form->setContent("§7Merci de sélectionner une catégorie");
			$form->addButton("§aRush", 0);
			$form->addButton("§aHikabrain", 1);
			$form->addButton("§aBlitz", 2);
     $form->addButton("§aSkywars", 3);
			$form->addButton("§cQuitter", 4);
			$form->sendToPlayer($sender);
			break;
		case "rush":
        if(!($sender instanceof Player)){
                $sender->sendMessage("§4 Tu ne peut pas utiliser ici");
                return true;
        }
        $api = $this->plugin->getServer()->getPluginManager()->getPlugin("FormAPI");
        $form = $api->createSimpleForm(function (Player $sender, $data){
            $result = $data;
            if ($result == null) {
            }
            switch($result) {
                    case 0:
                    $command = "rush join 1v1";
								$this->plugin->getServer()->getCommandMap()->dispatch($sender, $command);
					$sender->sendMessage(" §2Ton ile à été crée !");
						break;
                    case 1:
                    $command = "rush join 2v2";
								$this->plugin->getServer()->getCommandMap()->dispatch($sender, $command);
					$sender->sendMessage(" §2Ton ile à été supprimée");
						break;
					case 2:
                    $command = "rush join 4v4";
								$this->plugin->getServer()->getCommandMap()->dispatch($sender, $command);
						break;
					case 3:
                    $command = "compass";
								$this->plugin->getServer()->getCommandMap()->dispatch($sender, $command);
                        break;
            }
        });
        $form->setTitle("§5Menu du Rush");
        $form->setContent("§7Merci de sélectionner.");
        $form->addButton("§bRush\n§r§71v1", 0);
        $form->addButton("§bRush\n§r§72v2", 1);
        $form->addButton("§bRush\n§r§74v4", 2);
        $form->addButton("§cRetour au Menu", 3);
        $form->sendToPlayer($sender);
        break;
        case "hikabrain":
        if(!($sender instanceof Player)){
                $sender->sendMessage("§4 Tu ne peut pas utiliser ici");
                return true;
        }
        $api = $this->plugin->getServer()->getPluginManager()->getPlugin("FormAPI");
        $form = $api->createSimpleForm(function (Player $sender, $data){
            $result = $data;
            if ($result == null) {
            }
            switch ($result) {
                    case 0:
                    $command = "hikabrain join 1v1";
								$this->plugin->getServer()->getCommandMap()->dispatch($sender, $command);
				    $sender->sendMessage("§2Joining 1v1 hikabrain...");
                        break; 
                   case 1:
                    $command = "hikabrain join 2v2";
								$this->plugin->getServer()->getCommandMap()->dispatch($sender, $command);
				    $sender->sendMessage("§2Joining 2v2 hikabrain");
                        break; 
                    case 2:
                    $command = "compass";
								$this->plugin->getServer()->getCommandMap()->dispatch($sender, $command);
                        break;
           }
        });
        $form->setTitle("§5Menu Hikabrain");
        $form->setContent("§7Selectionne un jeu");
        $form->addButton("§bHikabrain\n§71v1 ", 0);
        $form->addButton("§bHikabrain\n§72v2", 1);
        $form->addButton("§cRetour au Menu", 2);
        $form->sendToPlayer($sender);
		}
		return true;
	}	

	public function onDropItem(PlayerDropItemEvent $e) {
		$i = $e->getItem()->getCustomName();
		if($i == "§l§3Navigator") {
			$e->setCancelled();
		}
	}
	} 