<?php

declare(strict_types=1);

namespace Hirss\commands\presets;


use Hirss\commands\PartyCommand;
use Hirss\parties\session\Session;
use pocketmine\utils\TextFormat;

class LockCommand extends PartyCommand {

    /**
     * LockCommand constructor.
     */
    public function __construct() {
        parent::__construct(["lock"], "/party lock/unlock", "Locks/unlocks the party to make players be able to join it");
    }

    /**
     * @param Session $session
     * @param array $args
     */
    public function onCommand(Session $session, array $args): void {
        if(!$session->hasParty()) {
            $session->sendMissingPartyMessage();
            return;
        }
        $party = $session->getParty();
        if(!$session->isLeader()) {
            $session->sendLeaderMessage();
            return;
        }
        $username = $session->getUsername();
        if($party->isLocked()) {
            $party->setLocked(false);
            $party->sendMessage(TextFormat::GREEN . $username . " has unlocked the party!");
            return;
        }
        $party->setLocked();
        $party->sendMessage(TextFormat::GREEN . $username . " has locked the party!");
    }

}