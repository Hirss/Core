<?php
namespace Hirss\tasks;
use pocketmine\scheduler\Task;
use pocketmine\Player;
use Hirss\Main;
class PlayerHealTask extends Task{
	public function __construct(Main $plugin){
		$this->plugin = $plugin;
	}
		public function onRun($currentTick){
		foreach($this->plugin->getServer()->getOnlinePlayers() as $player){
			$tag = $this->plugin->config->get("heal-task");
			$tag = str_replace("{health}", $player->getHealth(), $tag);
		    $tag = str_replace("{maxhealth}", $player->getMaxHealth(), $tag);
			$player->setNameTagVisible();
			$player->setScoreTag($tag);
		}
	}
}