<?php

declare(strict_types=1);

namespace Hirss\parties\session;


use Hirss\events\session\SessionCloseEvent;
use Hirss\events\session\SessionOpenEvent;
use Hirss\Main;
use Hirss\parties\session\SessionListener;
use pocketmine\Player;

class SessionManager {
	
private $plugin;

    /** @var Session[] */
    private $sessions = [];

    /**
     * SessionManager constructor.
     * @param Parties $plugin
     */
    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
        $plugin->getServer()->getPluginManager()->registerEvents(new SessionListener($this), $plugin);
    }


    /**
     * @return Parties
     */
    public function getPlugin(): Main {
        return $this->plugin;
    }

    /**
     * @return Session[]
     */
    public function getSessions(): array {
        return $this->plugin->sessions;
    }

    /**
     * @param Player $player
     * @return Session
     */
    public function getSession(Player $player): Session {
        return $this->plugin->sessions[$player->getName()];
    }

    /**
     * @param Player $player
     */
    public function openSession(Player $player): void {
        if(!isset($this->plugin->sessions[$username = $player->getName()])) {
            $session = new Session($this, $player);
            $this->plugin->sessions[$username] = $session;
            $this->plugin->getServer()->getPluginManager()->callEvent(new SessionOpenEvent($session));
        }
    }

    /**
     * @param Player $player
     */
    public function closeSession(Player $player): void {
        if(isset($this->plugin->sessions[$username = $player->getName()])) {
            $session = $this->plugin->sessions[$username];
            $session->clearInvitations();
            $this->plugin->getServer()->getPluginManager()->callEvent(new SessionCloseEvent($session));
            unset($session);
        }
    }

}