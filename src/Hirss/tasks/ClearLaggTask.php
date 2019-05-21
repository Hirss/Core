<?php

declare(strict_types=1);

namespace Hirss\tasks;

use pocketmine\entity\Creature;
use pocketmine\entity\Entity;
use pocketmine\entity\Human;
use pocketmine\scheduler\Task;
use Hirss\API;
use pocketmine\utils\TextFormat;
use Hirss\Main;

class ClearLaggTask extends Task{

	/** @var array $exemptedEntities */
	private $exemptedEntities = [];

	public function onRun(int $tick) : void{
		if(API::getMainInstance()->getConfig()->get("clearlagg") === "on"){
			$this->clearItems();
			$this->clearMobs();
			API::getMainInstance()->getServer()->broadcastMessage(Main::PREFIX . TextFormat::GREEN . "Cleared excess entities");
		}
	}

	private function clearItems() : int{
		$i = 0;
		foreach(API::getMainInstance()->getServer()->getLevels() as $level){
			foreach($level->getEntities() as $entity){
				if(!$this->isEntityExempted($entity) && !($entity instanceof Creature)){
					$entity->close();
					$i++;
				}
			}
		}
		return $i;
	}

	private function clearMobs() : int{
		$i = 0;
		foreach(API::getMainInstance()->getServer()->getLevels() as $level){
			foreach($level->getEntities() as $entity){
				if(!$this->isEntityExempted($entity) && $entity instanceof Creature && !($entity instanceof Human)){
					$entity->close();
					$i++;
				}
			}
		}
		return $i;
	}

	private function isEntityExempted(Entity $entity) : bool{
		return isset($this->exemptedEntities[$entity->getID()]);
	}
}
