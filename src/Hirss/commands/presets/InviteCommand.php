<?php

declare(strict_types=1);

namespace Hirss\commands\presets;


use Hirss\commands\PartyCommand;
use Hirss\parties\session\Session;
use pocketmine\utils\TextFormat;

class InviteCommand extends PartyCommand {

    /**
     * InviteCommand constructor.
     */
    public function __construct() {
        parent::__construct(["invite"], "/party invite (player)", "Invites the player to your party, creating one if you haven't done it");
    }

    /**
     * @param Session $session
     * @param array $args
     */
    public function onCommand(Session $session, array $args): void {
        if(!isset($args[0])) {
            $session->sendMessage("Usage: " .$this->getUsageMessageId());
            return;
        }
        $plugin = $session->getManager()->getPlugin();
        $player = $plugin->getServer()->getPlayer($args[0]);
        if($player == null) {
            $session->sendValidPlayerMessage();
            return;
        }
        $playerSession = $session->getManager()->getSession($player);
        if($playerSession->getUsername() == $session->getUsername()) {
            $session->sendMessage(TextFormat::RED . "You can't invite yourself!");
            return;
        }
        if($playerSession->hasParty()) {
            $session->sendMessage(TextFormat::RED . "This player is already in a party!");
            return;
        }
        if(!$session->hasParty()) {
            $plugin->getServer()->dispatchCommand($session->getOwner(), "party create");
        }
        if(!$session->isLeader()) {
            $session->sendLeaderMessage();
            return;
        }
        $username = $session->getUsername();
        $playerSession->addInvitationFrom($session);
        $playerSession->sendMessage(TextFormat::AQUA . $username . " has invited you to a party! Use " . TextFormat::WHITE .  "/party accept $username" . TextFormat::AQUA . " to accept the invitation");
        $session->sendMessage(TextFormat::GREEN . "You have invited {$playerSession->getUsername()} to the party!");
    }

}