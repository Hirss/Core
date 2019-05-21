<?php

namespace Hirss;

use pocketmine\entity\Entity;
use pocketmine\network\mcpe\protocol\AddEntityPacket;
use pocketmine\network\mcpe\protocol\RemoveEntityPacket;
use pocketmine\network\mcpe\protocol\SetEntityDataPacket;
use pocketmine\network\mcpe\protocol\UpdateAttributesPacket;
use pocketmine\Player;
use pocketmine\Server;

class API{

	public static $instance;

	public static function getMainInstance() : Main{
		return self::$instance;
	}

	public static function setMotd(string $motd) : void{
		API::getMainInstance()->getServer()->getNetwork()->setName($motd);
	}
}
