<?php

declare(strict_types=1);

namespace Hirss\parties\session;


use pocketmine\event\Listener;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;

class SessionListener implements Listener {
	
private $plugin;

/** public function __construct($plugin) {
$this->plugin = $plugin;
} */
    /** @var SessionManager */
    private $manager;

    /**
     * SessionListener constructor.
     * @param SessionManager $manager
     */
public function __construct(SessionManager $manager) {
        $this->manager = $manager;
    }

    /**
     * @param PlayerLoginEvent $event
     */
    public function onLogin(PlayerLoginEvent $event): void {
        $this->manager->openSession($event->getPlayer());
    }

    /**
     * @param PlayerQuitEvent $event
     */
    public function onQuit(PlayerQuitEvent $event): void {
        $this->manager->closeSession($event->getPlayer());
    }

}