<?php
//Memo: Just found out about PhpDoc. I should maybe try that out soon.

//Command
abstract class Command
{
	//Properties
	protected $name = "Command";
	protected $desc = "Command description. This will be displayed by the help command, typically.";
	protected $bot;
	
	//Methods
	function __construct($theBot)
	{
		if (isset($theBot)) {$this->bot = $theBot;}
		else
		{
			echo "WARNING: The command $this->name does not have a reference to theBot with which to perform actions.";
		}		
	}
	
	//Check for this name as the first portion of the string before acting.

	
	//All of these conditions must be true to perform this command.
	abstract function ConditionsMet(IRCMessage $ircmsg);
	/*
	{
		//You should overwrite this in your other commands to check conditions specific to it and return true if they match, false if they don't.
		$result = true;
		if (!isset($ircmsg))
		{
			$result = false;
		}
		return $result;
	}
	*/

	
	//What to perform if the conditions are met.
	abstract function Act(IRCMessage $ircmsg);
	/* {
		//Just do whatever you want the command to do.
		//If you want the bot to display some text, it may be better to return it than display it directly.
	} */
	
	//Letting the user know how to use a command.
	abstract function Help();
	/* {
		//Return a text description explaining the command's syntax and purpose.
		
		return " Syntax: $this->name \n $this->desc";
	} */
	
	//We could take the IRCMessage and use GetCommand within the method call, but that adds a dependency on the IRCMessage class. Let's compare stings instead.
	//... On second thought, a lot of the way this functions expects IRCMessages anyway for knowing how to handle Actions and Conditions, so it's OK to leave it as is. Probably.
	//That said, is there any reason the second argument shouldn't be of the command type?
	public static function MatchCommandString($ircmsg, $cmd)
	{
		$result = false;
		if (strtoupper($ircmsg->GetCommand()) === strtoupper($cmd))
		{
			$result = true;
		}
		return $result;
	}
	
	public static function MatchCommandStringRegexp($ircmsg, $pattern)
	{
		$result = false;
		$arr = preg_split($pattern, $ircmsg->GetMessage());
		$c = count($arr);
		if ($c > 1)
		{
			$result = true;
		}
		return $result;
	}
	
	public function GetName()
	{
		return $this->name;
	}

	
	//Accessors & Mutators
	
}