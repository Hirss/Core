<?php

declare(strict_types=1);

namespace Hirss\commands\presets;


use Hirss\commands\PartyCommand;
use Hirss\parties\session\Session;
use pocketmine\utils\TextFormat;

class CreateCommand extends PartyCommand {

    /**
     * CreateCommand constructor.
     */
    public function __construct() {
        parent::__construct(["create"], "/party create", "Creates a party");
    }

    /**
     * @param Session $session
     * @param array $args
     */
    public function onCommand(Session $session, array $args): void {
        if($session->hasParty()) {
            $session->sendAlreadyPartyMessage();
            return;
        }
        $session->getManager()->getPlugin()->getPartyManager()->createParty($session);
        $session->sendMessage(TextFormat::GREEN . "You have created a party!");
    }

}