<?php

namespace Hirss;

use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\plugin\PluginBase as PL;
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

class Main extends PL implements Listener {
	
    public function onEnable(){
		$this->getServer()->getLogger()->info(TF::GREEN . "v1.0.0 Enabled!");
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args): bool{
        switch($cmd->getName()){
		
        case "pui":
		
			if(!($sender instanceof Player)){
			}
		
			$api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
			$form = $api->createSimpleForm(function (Player $sender, $data){
            $result = $data;
            if ($result == null) {
            }
            switch($result) {
                case 0:
                //party invite UI
                    $command = "piui";
					$this->getServer()->getCommandMap()->dispatch($sender, $command);
				break;
                case 1:
                    $command = "party leave";
					$this->getServer()->getCommandMap()->dispatch($sender, $command);
                break;
                case 2:
                    $command = "party lock";
					$this->getServer()->getCommandMap()->dispatch($sender, $command);
				break;
                case 3:
                    $command = "party create";
					$this->getServer()->getCommandMap()->dispatch($sender, $command);
				break;
              case 4:
                    $command = "party disband";
					$this->getServer()->getCommandMap()->dispatch($sender, $command);
				break;
              case 5:
				break;
            }
			});
			$form->setTitle("§4Party UI");
			$form->setContent("§7Merci de sélectionner une catégorie");
			$form->addButton("§aParty invite", 0);
			$form->addButton("§aLeave party", 1);
			$form->addButton("§aParty lock", 2);
			$form->addButton("§aParty Create", 3);
     $form->addButton("§cParty Disband", 4);
			$form->addButton("§cQuitter", 5);
			$form->sendToPlayer($sender);
			break;
		case "piui":
        if(!($sender instanceof Player)){
                $sender->sendMessage("§4 Tu ne peut pas utiliser ici");
                return true;
        }
        $api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
        $form = $api->createSimpleForm(function (Player $sender, $data){
            $result = $data;
            if ($result == null) {
            }
        });
        $form->setTitle("§5Invite friend");
        $form->setContent("Write his name.");
        $form->addInput("Maykeul_Jakssonne");
        $form->sendToPlayer($sender);
        break;

	public function onInteract(PlayerInteractEvent $event){
    $item = $event->getItem();
	$player = $event->getPlayer();
	$itemname = $item->getCustomName();
    if ($itemname === "§4Party"){
		$player->getServer()->dispatchCommand($player, "pui");
		}
	}
	
	public function onDropItem(PlayerDropItemEvent $e) {
		$i = $e->getItem()->getCustomName();
		if($i == "§4Party") {
			$e->setCancelled();
		}
	}
	
    public function onDisable(){
        $this->getServer()->getLogger()->info(TF::GREEN . "v1.0.0 Disabled!");
    }
}
