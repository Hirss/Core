<?php

/**
  *   _______             _
  *  |  _____|           | |                     _
  *  | |__               | |                    | |
  *  | ___|__  _ ___ ___ | |     __ _  _ __   __| | 
  *  | | / _ \| '__// __|| |    / _` || '_ \ / _  |
  *  | || (_) | |   \__ \| |___| (_| || | | | (_| |
  *  |_| \___/|_|   |___/|_____|\__,_||_| |_|\__,_|
  *
  * Based on: https://github.com/jojoe77777/FormAPI
  *
  * @Author: Saehing
  * @Website: ForsLand.ru
  * @Vk: vk.com/fors_land_mcpe
  *
  * This program is free software: you can redistribute it and/or modify
  * it under the terms of the GNU General Public License as published by
  * the Free Software Foundation, either version 3 of the License, or
  * (at your option) any later version.
  *
  * This program is distributed in the hope that it will be useful,
  * but WITHOUT ANY WARRANTY; without even the implied warranty of
  * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  * GNU General Public License for more details.
  *
  * You should have received a copy of the GNU General Public License
  * along with this program.  If not, see <http://www.gnu.org/licenses/>.
  *
  *
  *
  * Copyright (C) 2017 ForsLand
  *
  **/

declare(strict_types = 1);

namespace FormAPI;

use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;

class boot extends PluginBase implements Listener {

	/** @var int */
	public static $formCount = 0;
	/** @var array */
	public static $forms = [];

    public static function register(PluginBase $plugin): void{
        Server::getInstance()->getPluginManager()->registerEvents(new EventListener, $plugin);
    }

	/**
	 * @param callable $function
	 * @return CustomForm
	 */
	public static function createCustomForm(callable $function = null): CustomForm{
		self::$formCount++;
		$form = new CustomForm(self::$formCount, $function);
		if($function !== null){
			self::$forms[self::$formCount] = $form;
		}
		return $form;
	}

    /**
     * @param callable|null $function
     * @return SimpleForm
     */
    public static function createSimpleForm(callable $function = null): SimpleForm{
		self::$formCount++;
		$form = new SimpleForm(self::$formCount, $function);
		if($function !== null){
			self::$forms[self::$formCount] = $form;
		}
		return $form;
	}
}
