<?php

declare(strict_types=1);

namespace Hirss\commands\presets;


use Hirss\commands\PartyCommand;
use Hirss\parties\session\Session;
use pocketmine\utils\TextFormat;

class LeaveCommand extends PartyCommand {

    /**
     * LeaveCommand constructor.
     */
    public function __construct() {
        parent::__construct(["leave"], "/party leave", "Leaves the current party");
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
        if($session->isLeader()) {
            $session->getManager()->getPlugin()->getServer()->dispatchCommand($session->getOwner(), "party disband");
            return;
        }
        $party->removeMember($session);
        $party->sendMessage(TextFormat::GREEN . $session->getUsername() . " has left the party");
        $session->sendMessage(TextFormat::AQUA . "You have left the party!");
    }

}