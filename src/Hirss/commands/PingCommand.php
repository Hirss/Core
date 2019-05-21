<?php

declare(strict_types=1);

namespace Hirss\commands;

use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use Hirss\Main;

class PingCommand extends BaseCommand{

	public function __construct(Main $main){
		parent::__construct($main, "ping", "Ping command", "/ping", ["ping"]);
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args) : bool{
		if(!$sender instanceof Player){
			$sender->sendMessage(Main::PREFIX . TextFormat::RED . "Use this command in-game");
			return false;
		}
		if(!$sender->hasPermission("ping.command")){
			$sender->sendMessage(self::NO_PERMISSION);
			return false;
		}
		$sender->sendMessage(Main::PREFIX . TextFormat::GREEN . "Your ping is " . $sender->getPing() . " ms");
		return true;
	}
}