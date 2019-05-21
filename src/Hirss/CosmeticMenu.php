<?php

namespace Hirss;

use pocketmine\event\entity\ExplosionPrimeEvent;
use pocketmine\event\entity\ProjectileHitEvent;
use pocketmine\event\entity\ProjectileLaunchEvent;
use pocketmine\level\particle\RedstoneParticle;
use pocketmine\utils\Config;
use pocketmine\level\Level;
use pocketmine\scheduler\Task as PluginTask;
use pocketmine\level\particle\HugeExplodeParticle;
use pocketmine\level\particle\WaterParticle;
use pocketmine\level\particle\AngryVillagerParticle;
use pocketmine\entity\Arrow;
use pocketmine\entity\projectile\EnderPearl;
use pocketmine\utils\Random;
use pocketmine\entity\Snowball;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\entity\Entity;
use pocketmine\math\Vector3;
use pocketmine\block\Air;
use pocketmine\network\mcpe\protocol\AddItemEntityPacket;
use pocketmine\event\player\PlayerRespawnEvent;
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

class CosmeticMenu implements Listener {
private $plugin;
public function __construct($plugin) {
$this->plugin = $plugin;
}

    public function onIntermarcher(PlayerInteractEvent $event) {
    $player = $event->getPlayer();
        $name = $player->getName();
        if ($player instanceof Player) {
            $block = $player->getLevel()->getBlock($player->floor()->subtract(0, 1));
            $item = $player->getInventory()->getItemInHand();
            $level = $player->getLevel();
            if ($item->getId() == 341) {
                $block = $event->getBlock();
                $pos = new Vector3($block->getX(), $block->getY() + 2, $block->getZ());
                $particle = new RedstoneParticle($pos, 5);
                $particle2 = new HugeExplodeParticle($pos, 5);
                $particle3 = new WaterParticle($pos, 50);
                $particle4 = new AngryVillagerParticle($pos, 15);
                $level->addParticle($particle);
                $level->addParticle($particle2);
                $level->addParticle($particle3);
                $level->addParticle($particle4);
            }
            //Leaper
            if ($block->getId() === 0) {
                $player->sendPopup("§cPlease wait");
                return true;
            }
            if ($item->getId() == 258) {
                $player->setMotion(new Vector3(0, 5, 0));
                $player->sendPopup("§aLeaped!");
            }
            //Egg Launcher
            if ($item->getId() == 329) {
                $nbt = new CompoundTag("", ["Pos" => new ListTag("Pos", [new DoubleTag("", $player->x), new DoubleTag("", $player->y + $player->getEyeHeight()), new DoubleTag("", $player->z) ]), "Motion" => new ListTag("Motion", [new DoubleTag("", -\sin($player->yaw / 180 * M_PI) * \cos($player->pitch / 180 * M_PI)), new DoubleTag("", -\sin($player->pitch / 180 * M_PI)), new DoubleTag("", \cos($player->yaw / 180 * M_PI) * \cos($player->pitch / 180 * M_PI)) ]), "Rotation" => new ListTag("Rotation", [new FloatTag("", $player->yaw), new FloatTag("", $player->pitch) ]) ]);
                $f = 1.0;
                $snowball = Entity::createEntity("Egg", $player->getLevel(), $nbt, $player);
                $snowball->setMotion($snowball->getMotion()->multiply($f));
                $snowball->spawnToAll();
            }
            if ($item->getId() === 351) { // Dye
                switch ($item->getDamage()) {
                    case 4: // lapis: water
                        if (!in_array($name, $this->plugin->water)) {
                            $this->plugin->water[] = $name;
                            if(in_array($name, $this->plugin->fire)) {
                                unset($this->plugin->fire[array_search($name, $this->plugin->fire)]);
                            } elseif(in_array($name, $this->plugin->heart)) {
                                unset($this->plugin->heart[array_search($name, $this->plugin->heart)]);
                            } elseif(in_array($name, $this->plugin->smoke)) {
                                unset($this->plugin->smoke[array_search($name, $this->plugin->smoke)]);
                            }
                            $player->sendMessage("§l§aYou have enabled your §6Water §aParticles");
                        } else {
                            unset($this->plugin->water[array_search($name, $this->plugin->water)]);
                            $player->sendMessage("§l§cYou have disabled your §6Water §cParticles");
                        }
                    break;
                    case 14: // orange: fire
                        if (!in_array($name, $this->plugin->fire)) {
                            $this->plugin->fire[] = $name;
                            if(in_array($name, $this->plugin->water)) {
                                unset($this->plugin->water[array_search($name, $this->plugin->water)]);
                            } elseif(in_array($name, $this->plugin->heart)) {
                                unset($this->plugin->heart[array_search($name, $this->plugin->heart)]);
                            } elseif(in_array($name, $this->plugin->smoke)) {
                                unset($this->plugin->smoke[array_search($name, $this->plugin->smoke)]);
                            }
                            $player->sendMessage("§l§aYou have enabled your §6Fire §aParticles");
                        } else {
                            unset($this->plugin->fire[array_search($name, $this->plugin->fire)]);
                            $player->sendMessage("§l§cYou have disabled your §6Fire §cParticles");
                        }
                    break;
                    case 1: // red: heart
                        if (!in_array($name, $this->plugin->heart)) {
                            $this->plugin->heart[] = $name;
                            if(in_array($name, $this->plugin->water)) {
                                unset($this->plugin->water[array_search($name, $this->plugin->water)]);
                            } elseif(in_array($name, $this->plugin->fire)) {
                                unset($this->plugin->fire[array_search($name, $this->plugin->fire)]);
                            } elseif(in_array($name, $this->plugin->smoke)) {
                                unset($this->plugin->smoke[array_search($name, $this->plugin->smoke)]);
                            }
                            $player->sendMessage("§l§aYou have enabled your §6Heart §aParticles");
                        } else {
                            unset($this->plugin->heart[array_search($name, $this->plugin->heart)]);
                            $player->sendMessage("§l§cYou have disabled your §6Heart §cParticles");
                        }
                    break;
                    case 15: // white: smoke
                        if (!in_array($name, $this->plugin->smoke)) {
                            $this->plugin->smoke[] = $name;
                            if(in_array($name, $this->plugin->water)) {
                                unset($this->plugin->water[array_search($name, $this->plugin->water)]);
                            } elseif(in_array($name, $this->plugin->fire)) {
                                unset($this->plugin->fire[array_search($name, $this->plugin->fire)]);
                            } elseif(in_array($name, $this->plugin->heart)) {
                                unset($this->plugin->heart[array_search($name, $this->plugin->heart)]);
                            }
                            $player->sendMessage("§l§aYou have enabled your §6Smoke §aParticles");
                        } else {
                            unset($this->plugin->smoke[array_search($name, $this->plugin->smoke)]);
                            $player->sendMessage("§l§cYou have disabled your §6Smoke §cParticles");
                        }
                    break;
                }
            }
            //TNTLauncher
            if ($item->getId() == 352) {
                foreach ($player->getInventory()->getContents() as $item) {
                    $nbt = new CompoundTag("", ["Pos" => new ListTag("Pos", [new DoubleTag("", $player->x), new DoubleTag("", $player->y + $player->getEyeHeight()), new DoubleTag("", $player->z) ]), "Motion" => new ListTag("Motion", [new DoubleTag("", -\sin($player->yaw / 180 * M_PI) * \cos($player->pitch / 180 * M_PI)), new DoubleTag("", -\sin($player->pitch / 180 * M_PI)), new DoubleTag("", \cos($player->yaw / 180 * M_PI) * \cos($player->pitch / 180 * M_PI)) ]), "Rotation" => new ListTag("Rotation", [new FloatTag("", $player->yaw), new FloatTag("", $player->pitch) ]) ]);
                    $f = 3.0;
                    $snowball = Entity::createEntity("PrimedTNT", $player->getLevel(), $nbt, $player);
                    $snowball->setMotion($snowball->getMotion()->multiply($f));
                    $snowball->spawnToAll();
                }
            }
            //Items
            if ($item->getId() == 347) {
                $player->getInventory()->removeItem(Item::get(ITEM::CLOCK));
                $player->getInventory()->addItem(Item::get(ITEM::MINECART));
                $player->getInventory()->addItem(Item::get(ITEM::REDSTONE));
           } 
            //Gadgets
            if ($item->getid() == 328) {
                $player->getInventory()->removeItem(Item::get(ITEM::CLOCK));
                $player->getInventory()->removeItem(Item::get(ITEM::MINECART));
                $player->getInventory()->removeItem(Item::get(ITEM::REDSTONE));
                $player->getInventory()->removeItem(Item::get(ITEM::DIAMOND_HELMET));
                $player->getInventory()->addItem(Item::get(ITEM::BED));
                $player->getInventory()->addItem(Item::get(ITEM::SADDLE));
                $player->getInventory()->addItem(Item::get(ITEM::SLIMEBALL));
                $player->getInventory()->addItem(Item::get(ITEM::IRON_AXE));
                $player->getInventory()->addItem(Item::get(ITEM::ENDER_PEARL, 0, 1));
                $player->getInventory()->addItem(Item::get(ITEM::BONE));
           } 
            //Particle
            if ($item->getid() == 331) {
                $player->getInventory()->removeItem(Item::get(ITEM::CLOCK));
                $player->getInventory()->removeItem(Item::get(ITEM::MINECART));
                $player->getInventory()->removeItem(Item::get(ITEM::REDSTONE));
                $player->getInventory()->addItem(Item::get(ITEM::BED));
                $player->getInventory()->addItem(Item::get(ITEM::DYE, 4, 1));
                $player->getInventory()->addItem(Item::get(ITEM::DYE, 14, 1));
                $player->getInventory()->addItem(Item::get(ITEM::DYE, 1, 1));
                $player->getInventory()->addItem(Item::get(ITEM::DYE, 15, 1));
            }
            //Back
            if ($item->getId() == 355) {
                $player->getInventory()->removeItem(Item::get(ITEM::BED));
                $player->getInventory()->removeItem(Item::get(ITEM::SLIMEBALL));
                $player->getInventory()->removeItem(Item::get(ITEM::ENDER_PEARL, 0, 10000));
                $player->getInventory()->removeItem(Item::get(ITEM::IRON_AXE));
                $player->getInventory()->removeItem(Item::get(ITEM::MINECART));
                $player->getInventory()->removeItem(Item::get(ITEM::REDSTONE));
                $player->getInventory()->removeItem(Item::get(ITEM::STEAK));
                $player->getInventory()->removeItem(Item::get(ITEM::SEEDS));
                $player->getInventory()->removeItem(Item::get(ITEM::COOKIE));
                $player->getInventory()->removeItem(Item::get(ITEM::PAPER));
                $player->getInventory()->removeItem(Item::get(ITEM::BUCKET));
                $player->getInventory()->removeItem(Item::get(ITEM::SADDLE));
                $player->getInventory()->removeItem(Item::get(ITEM::DYE, 15, 1));
                $player->getInventory()->removeItem(Item::get(ITEM::DYE, 4, 1));
                $player->getInventory()->removeItem(Item::get(ITEM::DYE, 1, 1));
                $player->getInventory()->removeItem(Item::get(ITEM::DYE, 14, 1));
                $player->getInventory()->removeItem(Item::get(ITEM::BONE));
                $player->getInventory()->addItem(Item::get(ITEM::CLOCK));
                $player->getArmorInventory()->setHelmet(Item::get(ITEM::AIR));
                $player->getArmorInventory()->setChestplate(Item::get(ITEM::AIR));
                $player->getArmorInventory()->setLeggings(Item::get(ITEM::AIR));
                $player->getArmorInventory()->setBoots(Item::get(ITEM::AIR));
            }
        }

	}
	
    public function onPlayerItemHeldEvent(PlayerItemHeldEvent $e) {
        $i = $e->getItem();
        $p = $e->getPlayer();
        //ItemNames
        if ($i->getId() == 347) {
            $p->sendPopup("§l§dCosmetic§eMenu");
        }
        //Gadgets
        if ($i->getId() == 328) {
            $p->sendPopup("§l§6Gadgets");
        }
        //EggLauncher
        if ($i->getId() == 329) {
            $p->sendPopup("§l§6Egg§bLauncher");
        }
        //EnderPearl
        if ($i->getId() == 368) {
            $p->sendPopup("§l§dEnderPearl");
        }
        //BunnyHop
        if ($i->getId() == 258) {
            $p->sendPopup("§l§bBunnyHop");
        }
        //FlyTime
        if ($i->getId() == 288) {
            $p->sendPopup("§l§6FlyTime");
        }
        //ParticleBomb
        if ($i->getId() == 341) {
            $p->sendPopup("§l§dParticle§eBomb");
        }
        //LightningStick
        if ($i->getId() == 352) {
            $p->sendPopup("§l§6Lighting§aStick");
        }
        //Partical
        if ($i->getId() == 331) {
            $p->sendPopup("§l§bParticles");
        }
        //Water
        if ($i->getId() == 351 && $i->getDamage() == 4) {
            $p->sendPopup("§l§6Water");
        }
        //Fire
        if ($i->getId() == 351 && $i->getDamage() == 14) {
            $p->sendPopup("§l§6Fire");
        }
        //Hearts
        if ($i->getId() == 351 && $i->getDamage() == 1) {
            $p->sendPopup("§l§6Hearts");
        }
        //Smoke
        if ($i->getId() == 351 && $i->getDamage() == 15) {
            $p->sendPopup("§l§6Smoke");
        }
        //Back
        if ($i->getId() == 355) {
            $p->sendPopup("§l§7Back...");
        }
        //TNTLauncher
        if ($i->getId() == 352) {
            $p->sendPopup("§l§cTNT§aLauncher");
        }
    }
    public function ExplosionPrimeEvent(ExplosionPrimeEvent $p) {
        $p->setBlockBreaking(false);
    }
}
