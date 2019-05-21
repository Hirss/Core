<?php

declare(strict_types=1);

namespace Hirss\commands;

use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use Hirss\Main;

class GodCommand extends BaseCommand{

	/** @var array $god */
	public static $god = [];

	public function __construct(Main $main){
		parent::__construct($main, "god", "Allow yourself to not get damaged", "/god", ["god"]);
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args) : bool{
		if(!$sender instanceof Player){
			$sender->sendMessage(Main::PREFIX . TextFormat::RED . "Use this command in-game");
			return false;
		}
		if(!$sender->hasPermission("god.command")){
			$sender->sendMessage(self::NO_PERMISSION);
			return false;
		}
		if(!in_array($sender->getName(), self::$god)){
			self::$god[] = $sender->getName();
			$sender->sendMessage(Main::PREFIX . TextFormat::GREEN . "You have turned on god mode");
		}elseif(in_array($sender->getName(), self::$god)){
			unset(self::$god[array_search($sender->getName(), self::$god)]);
			$sender->sendMessage(Main::PREFIX . TextFormat::RED . "You have turned off god mode");
		}
		return true;
	}
}