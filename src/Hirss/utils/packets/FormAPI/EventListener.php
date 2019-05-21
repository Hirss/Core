<?php

namespace FormAPI;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\ModalFormResponsePacket;

class EventListener implements Listener {

    /**
     * @param DataPacketReceiveEvent $ev
     */
    public function onPacketReceived(DataPacketReceiveEvent $ev) : void {
        $pk = $ev->getPacket();
        if($pk instanceof ModalFormResponsePacket){
            $player = $ev->getPlayer();
            $formId = $pk->formId;
            $data = json_decode($pk->formData, true);
            if(isset(boot::$forms[$formId])){
                /** @var Form $form */
                $form = boot::$forms[$formId];
                if(!$form->isRecipient($player)){
                    return;
                }
                $callable = $form->getCallable();
                if(!is_array($data)){
                    $data = [$data];
                }
                if($callable !== null) {
                    $callable($ev->getPlayer(), $data);
                }
                unset(boot::$forms[$formId]);
                $ev->setCancelled();
            }
        }
    }

    /**
     * @param PlayerQuitEvent $ev
     */
    public function onPlayerQuit(PlayerQuitEvent $ev){
        $player = $ev->getPlayer();
        /**
         * @var int $id
         * @var Form $form
         */
        foreach(boot::$forms as $id => $form){
            if($form->isRecipient($player)){
                unset(boot::$forms[$id]);
                break;
            }
        }
    }
}