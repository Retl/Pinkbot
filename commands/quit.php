<?php
class Quit extends command
{
	//Properties
	protected $name = "Quit";
	protected $desc = "Forces the bot to disconnect from the network and shutdown immediately. Administrator only.";
	protected $bot;
	
	//Methods
	
	//Can't inherit a constructor from an abstract class.
	function __construct($theBot)
	{
		if (isset($theBot)) {$this->bot = $theBot;}
		else
		{
			echo "WARNING: The command $this->name does not have a reference to theBot with which to perform actions.";
		}		
	}
	
	//All of these conditions must be true to perform this command.
	function ConditionsMet(IRCMessage $ircmsg)
	{
		$result = false;
		if ((strtoupper($ircmsg->GetTarget()) === strtoupper($this->bot->GetNick())) && Command::MatchCommandString($ircmsg, $this->name) && count($ircmsg->GetArgs()) < 1) 
		{
			$result = true;
		}		
		return $result;
	}

	
	//What to perform if the conditions are met.
	function Act(IRCMessage $ircmsg)
	{
		$result = '';
		if (strtoupper($ircmsg->GetNick()) === strtoupper($this->bot->GetAdminNick()))
		{
			$this->Reply($ircmsg, "Okie doki loki! Later!");
			$this->bot->Quit();
			break(2);
		}
		else
		{
			$badguy = $ircmsg->GetNick();
			$goodguy = $this->bot->GetAdminNick();
			$denyList = [];
			$denyList[] = "Um. No. Maybe when you're older. Did you mean Quiche?";
			$denyList[] = "WARNING: Pushing that requires PARTY PINK level clearance. Ask $goodguy for assistance.";
			$denyList[] = "You wouldn't like it if I shutdown your things without permission, would you?";
			$denyList[] = "Eeeeeep! -o.o- No touchy! No touchy!";
			$denyList[] = "Maybe if $goodguy asks reaaaaal nice.";
			$denyList[] = "~Naaaauuughtyyyyy~";
			
			$result = $denyList[array_rand($denyList)];
		}
		return $result;
	}
	
	//Letting the user know how to use a command.
	function Help()
	{
		//Return a text description explaining the command's syntax and purpose.
		return "$this->name: $this->desc\n(Syntax: " .$this->bot->GetNick() .", QUIT)";
	}	
}