<?php

declare(strict_types=1);

namespace Hirss\events\session;


use Hirss\events\PartiesEvent;
use Hirss\parties\session\Session;

abstract class SessionEvent extends PartiesEvent {

    /** @var Session */
    private $session;

    /**
     * SessionEvent constructor.
     * @param Session $session
     */
    public function __construct(Session $session) {
        $this->session = $session;
    }

    /**
     * @return Session
     */
    public function getSession(): Session {
        return $this->session;
    }

}