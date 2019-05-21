<?php

declare(strict_types=1);

namespace Hirss\commands;

use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use Hirss\API;
use Hirss\Main;

class ClearInventoryCommand extends BaseCommand{

	public function __construct(Main $main){
		parent::__construct($main, "clearinv", "Clear your inventory", "/clearinv <player>", ["clearinv", "ci"]);
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args) : bool{
		if(!$sender instanceof Player){
			$sender->sendMessage(TextFormat::RED . "Use this command in-game");
			return false;
		}
		if(!$sender->hasPermission("clearinv.command")){
			$sender->sendMessage(self::NO_PERMISSION);
			return false;
		}
		if(empty($args[0])){
			$sender->getInventory()->clearAll();
			$sender->sendMessage(TextFormat::GREEN . "You have cleared your inventory");
			return false;
		}
		if(API::getMainInstance()->getServer()->getPlayer($args[0])){
			$player = API::getMainInstance()->getServer()->getPlayer($args[0]);
			$player->getInventory()->clearAll();
			$player->sendMessage(TextFormat::GREEN . "Your inventory has been cleared");
			$sender->sendMessage(TextFormat::GREEN . "You have cleared " . $player->getName() . "'s inventory");
		}
		return true;
	}
}