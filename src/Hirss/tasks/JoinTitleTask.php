<?php

declare(strict_types=1);

namespace Hirss\tasks;

use pocketmine\Player;
use pocketmine\scheduler\Task;
use Hirss\API;
use Hirss\Main;

class JoinTitleTask extends Task{

	/** @var Player $player */
	private $player;

	public function __construct(Main $main, Player $player){
		$this->player = $player;
		$this->main = $main;
	}

	public function onRun(int $tick) : void{
		
		$title = API::getMainInstance()->getConfig()->get("join-title");
		$subtitle = API::getMainInstance()->getConfig()->get("join-title");
		$title = str_replace("{playername}", $this->player->getName(), $title);
		$subtitle = str_replace("{playername}", $this->player->getName(), $subtitle);

		$this->player->addTitle($title, $subtitle);
	}
}
