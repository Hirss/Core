<?php

declare(strict_types=1);

namespace Hirss\commands\presets;


use Hirss\commands\PartyCommand;
use Hirss\parties\session\Session;
use pocketmine\utils\TextFormat;

class ListCommand extends PartyCommand {

    /**
     * ListCommand constructor.
     */
    public function __construct() {
        parent::__construct(["list"], "/party list", "Lists the members of your party");
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
        $session->sendMessage(TextFormat::GREEN . $party->getLeader()->getUsername() . "'s Party:");
        foreach($party->getMembers() as $member) {
            $session->sendMessage(TextFormat::GREEN . $member->getUsername());
        }
    }

}