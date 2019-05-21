<?php

declare(strict_types=1);

namespace Hirss\commands;

use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use Hirss\API;
use Hirss\Main;

class AfkCommand extends BaseCommand{

	public static $afk = [];

	public function __construct(Main $main){
		parent::__construct($main, "afk", "Allow yourself to be put in afk mode", "/afk", ["afk"]);
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args) : bool{
		if(!$sender instanceof Player){
			$sender->sendMessage(Main::PREFIX . TextFormat::RED . "Use this command in-game");
			return false;
		}
		if(!$sender->hasPermission("afk.command")){
			$sender->sendMessage(self::NO_PERMISSION);
			return false;
		}
		if(!in_array($sender->getName(), self::$afk)){
			self::$afk[] = $sender->getName();
			$sender->sendMessage(Main::PREFIX . TextFormat::GREEN . "You are now AFK");
			API::getMainInstance()->getServer()->broadcastMessage(TextFormat::YELLOW . $sender->getName() . " is now AFK");
		}elseif(in_array($sender->getName(), self::$afk)){
			unset(self::$afk[array_search($sender->getName(), self::$afk)]);
			$sender->sendMessage(Main::PREFIX . TextFormat::RED . "You have leave AFK");
			API::getMainInstance()->getServer()->broadcastMessage(TextFormat::YELLOW . $sender->getName() . " is no longer AFK");
		}
		return true;
	}
}