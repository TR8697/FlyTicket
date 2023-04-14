<?php

namespace FlyTicket\Command;

use FlyTicket\Form\FlyTicketForm;
use FlyTicket\Main;
use FlyTicket\Manager\FlyTicketManager;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class FlyTicketCommand extends Command
{

    public function __construct()
    {
        $command_name = Main::$config->get("komut");
        $command_description = Main::$config->get("komut-aciklamasi");
        parent::__construct($command_name, $command_description, "/".$command_name);
    }
    public function execute(CommandSender $player, string $commandLabel, array $args)
    {
        if ($player instanceof Player) {
            $player->sendForm(new FlyTicketForm($player));
        }else $player->sendMessage(FlyTicketManager::CONSOLE_USED);
    }
}