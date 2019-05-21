<?php

declare(strict_types=1);

namespace Hirss\commands\presets;


use Hirss\commands\PartyCommand;
use Hirss\parties\session\Session;
use pocketmine\utils\TextFormat;

class AcceptCommand extends PartyCommand {

    public function __construct() {
        parent::__construct(["accept"], "/party accept (player)", "Accepts a party invite from the player");
    }

    public function onCommand(Session $session, array $args): void {
        $player = null;
        if(!isset($args[0])) {
            if($session->hasLastInvitation()) {
                $player = $session->getLastInvitation()->getOwner();
            }
        } else {
            $player = $session->getManager()->getPlugin()->getServer()->getPlayer($args[0]);
        }
        if($player == null) {
            $session->sendValidPlayerMessage();
            return;
        }
        if($session->hasParty()) {
            $session->sendAlreadyPartyMessage();
            return;
        }
        $playerSession = $session->getManager()->getSession($player);
        if(!$session->hasInvitationFrom($playerSession)) {
            $session->sendMessage(TextFormat::RED . "You don't have an invitation from this player!");
            return;
        }
        $party = $playerSession->getParty();
        $party->addMember($session);
        $party->sendMessage(TextFormat::GREEN . $session->getUsername() . " has joined the party!");
        $session->sendMessage(TextFormat::AQUA . "You have joined " . TextFormat::WHITE . $playerSession->getUsername() . TextFormat::AQUA . "'s party!");
        $session->removeInvitationFrom($playerSession);
    }

}