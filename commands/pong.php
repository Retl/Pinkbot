<?php
//Help Command
class Pong extends command
{
	//Properties
	protected $name = "Pong";
	protected $desc = "Responds to the server's PING messages to keep the connection alive.";
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
		if (Command::MatchCommandString($ircmsg, $this->name)) 
		{
			$result = true;
		}	
		return $result;
	}

	
	//What to perform if the conditions are met.
	function Act(IRCMessage $ircmsg)
	{
		$this->bot->Speak("Arbitrary.");
		return $ircmsg->GetCommand() ." " .$ircmsg->GetMessage();
	}
	
	//Letting the user know how to use a command.
	function Help()
	{
		//Return a text description explaining the command's syntax and purpose.
		return "$this->name: $this->desc\n(This should only be invoked by the server.)";
	}	
}