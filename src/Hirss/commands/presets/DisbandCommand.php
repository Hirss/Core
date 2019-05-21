<?php

declare(strict_types=1);

namespace Hirss\commands\presets;


use Hirss\commands\PartyCommand;
use Hirss\parties\session\Session;
use pocketmine\utils\TextFormat;

class DisbandCommand extends PartyCommand {

    /**
     * DisbandCommand constructor.
     */
    public function __construct() {
        parent::__construct(["disband"], "/party disband", "Disbands the party");
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
        $party->sendMessage(TextFormat::AQUA . "The party has been disbanded!");
        $session->getManager()->getPlugin()->getPartyManager()->deleteParty($session);
    }

}