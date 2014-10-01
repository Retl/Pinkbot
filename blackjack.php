<?php
class Blackjack
{
	//Properties
	
	private $sessionID; //If two different groups of people are playing at the same time, don't mix them up.
	private $players; //All players participating. They should have specific nicks and their own hands.
	private $house; //Kinda like a special case of player which refers the one running the game.
	
	
	public function __construct($theSID, $thePlayers)
	{
		$this->suite = $theSuite;
		$this->value = $theValue;
		
		$firstChar = substr($theSuite,0,1);
		
		$this->name = $firstChar .$theValue;
	}
	
	public function GetName()
	{
		return $this->name;
	}
	
	public function GetValue()
	{
		return $this->value;
	}
	
	public function GetSuite()
	{
		return $this->suite;
	}
	
	public function __toString()
	{
		return $this->GetName();
	}
}