<?php

/*
 * WorldEditArt
 *
 * Copyright (C) 2016 LegendsOfMCPE
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author LegendsOfMCPE Team
 */

namespace WorldEditArt\User;

use pocketmine\level\Location;
use pocketmine\level\Position;
use pocketmine\permission\Permissible;
use WorldEditArt\DataProvider\Model\UserData;
use WorldEditArt\Objects\Space\Space;
use WorldEditArt\WorldEditArt;

abstract class WorldEditArtUser implements Permissible{
	/** @var WorldEditArt $main */
	private $main;
	/** @var UserData $data */
	private $data;
	/** @var CassetteQueue $queue */
	private $queue;

	/** @var Space[] $selections */
	private $selections = [];

	/** @var int $closeTime */
	private $closeTime = 0;

	public function __construct(WorldEditArt $main, UserData $data){
		$this->main = $main;
		$this->data = $data;
		$this->queue = new CassetteQueue($this);
	}

	public abstract function getType() : string;

	public abstract function getName() : string;

	public abstract function sendRawMessage(string $message);

	public abstract function getLocation() : Location;

	public final function getUniqueName() : string{
		return $this->getType() . "/" . $this->getName();
	}

	public function getLangs() : array{
		return $this->data->langs;
	}

	public function getData() : UserData{
		return $this->data;
	}

	public function translate(string $id, array $vars = []){
		return $this->main->translate($id, $this->getLangs(), $vars);
	}

	public function sendMessage(string $id, array $vars = []){
		$this->sendRawMessage((substr($id, 0, 5) === "%raw%") ? $id : $this->main->translate($id, $vars));
	}

	public function save(){
		$this->main->getDataProvider()->saveUserData($this->data);
	}

	public function isClosed() : bool{
		return $this->closeTime > 0; // oh no, we are facing the Year 2106 problem!
	}

	public function getCloseTime() : int{
		return $this->isClosed() ? -1 : (time() - $this->closeTime);
	}

	public function close(){
		$this->save();
		$this->closeTime = time();
	}

	/**
	 * @param string $name
	 *
	 * @return Space|null
	 */
	public function getSelection(string $name = "default"){
		return $this->selections[$name] ?? null;
	}

	public function setSelection(Space $space, string $name = "default"){
		$this->selections[$name] = $space;
	}

	public function canBuild(Position $pos) : bool{
		return true; // TODO under-construction zones
	}
}
