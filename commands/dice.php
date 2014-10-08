<?php
class Dice extends command
{
	//Properties
	protected $name = "Dice";
	protected $desc = "A fairly simple dice roller. Can roll up to 200 dice simultaneously. Largest die size is 1000. 1d100 and 1d10 are common in FoE PnP. 1d20 and 2d6 are also common for other games. Unlike most commands, you do not say the command's name to use it.";
	protected $bot;
	
	const MAXDIE = 200;
	const MAXSIDES = 1000;
	
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
		if(Command::MatchCommandString($ircmsg, $this->name) && count($ircmsg->GetArgs()) < 1)
		{
			$this->bot->Reply($ircmsg, $this->Help());
			$this->bot->Reply($ircmsg, $this->DoRoll("1d100"));
		}
		else if(Command::MatchCommandStringRegexp($ircmsg, '/^\d+[dD]\d+/'))
		{
			$result = true;
		}			
		return $result;
	}

	
	//What to perform if the conditions are met.
	function Act(IRCMessage $ircmsg)
	{
		//Consider adding a variant or argument that allows for exploding rolls?
		return $this->DoRoll($ircmsg->GetCommand());
		
	}
	
	function DoRoll($rollString)
	{
		$premsg = '';
		$explodedInput = explode("D", strtoupper($rollString));
		$numDie = $explodedInput[0];
		$numSides = $explodedInput[1];
		$roll = [];
		$sum = 0;
		
		if ($numDie > $this::MAXDIE)
		{
			$numDie = $this::MAXDIE;
			$premsg .= "Sorry, I can't do more than $numDie die at a time.\n";
		}
		
		if ($numSides > $this::MAXSIDES)
		{
			$numSides = $this::MAXSIDES;
			$premsg .= "It's not that a die with more than $numSides can't exist. It's just that they're really hard to read.\n";
		}
		
		for ($temp = $numDie; $temp > 0; $temp--)
		{
			$currentRoll = rand(1, $numSides);
			$roll[] = $currentRoll;
			$sum += $currentRoll;
		}
		
		$outmsg = $numDie ."d" .$numSides .": " .number_format($sum) ." = [" .implode(" + ", $roll) ."]";
		$outmsg = trim(chunk_split($outmsg, 400, "\n"));
		
		return $premsg .$outmsg;
	}
	
	//Letting the user know how to use a command.
	function Help()
	{
		//Return a text description explaining the command's syntax and purpose.
		return "$this->name: $this->desc\n(Syntax: '#d#' dice notation. For example, 1d100 rolls a single 100 sided die.)";
	}	
}