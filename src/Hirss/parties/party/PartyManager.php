<?php

declare(strict_types=1);

namespace Hirss\parties\party;


use Hirss\events\party\PartyCreateEvent;
use Hirss\events\party\PartyDisbandEvent;
use Hirss\events\party\PartyPromoteEvent;
use Hirss\Main;
use Hirss\parties\session\Session;

class PartyManager {
	
private $plugin;

    /** @var Party[] */
    private $parties = [];

    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
    }

    /**
     * @return Parties
     */
    public function getPlugin(): Main {
        return $this->plugin;
    }

    /**
     * @return Party[]
     */
    public function getParties(): array {
        return $this->plugin->parties;
    }

    /**
     * @param Session $session
     */
    public function renameParty(Session $session): void {
        if(isset($this->plugin->parties[$identifier = $session->getParty()->getIdentifier()])) {
            $party = $this->plugin->parties[$identifier];
            $party->setIdentifier($username = $session->getUsername());
            unset($this->plugin->parties[$identifier]);
            $this->plugin->parties[$username] = $party;
            $this->plugin->getPlugin()->getServer()->getPluginManager()->callEvent(new PartyPromoteEvent($party));
        }
    }

    /**
     * @param Session $session
     */
    public function createParty(Session $session): void {
        if(!isset($this->parties[$identifier = $session->getUsername()])) {
            $party = new Party($this, $identifier, $session);
            $session->clearInvitations();
            $this->parties[$identifier] = $party;
        }
    }

    /**
     * @param Session $session
     */
    public function deleteParty(Session $session): void {
        if(isset($this->parties[$identifier = $session->getUsername()])) {
            foreach($this->parties[$identifier]->getMembers() as $member) {
                $member->setParty(null);
            }
            $this->getPlugin()->getServer()->getPluginManager()->callEvent(new PartyDisbandEvent($this->plugin->parties[$identifier]));
            unset($this->parties[$identifier]);
        }
    }

}