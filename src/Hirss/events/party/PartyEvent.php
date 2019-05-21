<?php

declare(strict_types=1);

namespace Hirss\events\party;


use Hirss\events\PartiesEvent;
//use Hirss\parties\party\Party;
use Hirss\Main;

abstract class PartyEvent extends PartiesEvent {

    /** @var Party */
    private $party;

    /**
     * PartyEvent constructor.
     * @param Party $party
     */
    public function __construct(Main $party) {
        $this->party = $party;
    }

    /**
     * @return Party
     */
    public function getParty(): Party {
        return $this->party;
    }

}