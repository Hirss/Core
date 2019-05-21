<?php

declare(strict_types=1);

namespace Hirss\commands;


use Hirss\commands\presets\AcceptCommand;
use Hirss\commands\presets\ChatCommand;
use Hirss\commands\presets\CreateCommand;
use Hirss\commands\presets\DisbandCommand;
use Hirss\commands\presets\InviteCommand;
use Hirss\commands\presets\JoinCommand;
use Hirss\commands\presets\KickCommand;
use Hirss\commands\presets\LeaveCommand;
use Hirss\commands\presets\ListCommand;
use Hirss\commands\presets\LockCommand;
use Hirss\commands\presets\PromoteCommand;
use Hirss\Main;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class PartyCommandMap extends Command {

    /** @var Parties */
    private $plugin;

    /** @var PartyCommand[] */
    private $commands = [];

    /**
     * PartyCommandMap constructor.
     * @param Parties $plugin
     */
    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
        $this->registerCommand(new AcceptCommand());
        $this->registerCommand(new ChatCommand());
        $this->registerCommand(new CreateCommand());
        $this->registerCommand(new DisbandCommand());
        $this->registerCommand(new InviteCommand());
        $this->registerCommand(new JoinCommand());
        $this->registerCommand(new KickCommand());
        $this->registerCommand(new LeaveCommand());
        $this->registerCommand(new ListCommand());
        $this->registerCommand(new LockCommand());
        $this->registerCommand(new PromoteCommand());
        parent::__construct("party", "See party commands", "Usage: /party", ["p"]);
        $plugin->getServer()->getCommandMap()->register("party", $this);
    }

    /**
     * @return Parties
     */
    public function getPlugin(): Main {
        return $this->plugin;
    }

    /**
     * @return PartyCommand[]
     */
    public function getCommands(): array {
        return $this->commands;
    }

    /**
     * @param string $alias
     * @return null|PartyCommand
     */
    public function getCommand(string $alias): ?PartyCommand {
        foreach($this->commands as $key => $command) {
            if(in_array(strtolower($alias), $command->getAliases()) or $alias == $command->getName()) {
                return $command;
            }
        }
        return null;
    }

    /**
     * @param PartyCommand $command
     */
    public function registerCommand(PartyCommand $command) {
        $this->commands[] = $command;
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): void {
        if(!$sender instanceof Player) {
            $sender->sendMessage("Please, run this command in game");
            return;
        }
        $session = $this->plugin->getSessionManager()->getSession($sender);
        if(isset($args[0]) and $this->getCommand($args[0]) != null) {
            $this->getCommand(array_shift($args))->onCommand($session, $args);
        } else {
            $sender->sendMessage(TextFormat::GREEN . "Party Commands:");
            foreach($this->commands as $key => $command) {
                $sender->sendMessage(TextFormat::BOLD . TextFormat::DARK_GRAY . "» " . TextFormat::RESET . TextFormat::AQUA . $command->getUsageMessageId() . TextFormat::DARK_GRAY . " - " . TextFormat::GREEN .  $command->getDescriptionMessageId());
            }
        }
    }

}