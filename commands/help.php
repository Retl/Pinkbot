<?php
//Help Command
class Help extends command
{
	//Properties
	protected $name = "Help";
	protected $desc = "This is the command that gives help. Yay!";
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
	
	//Check for this name as the first portion of the string before acting.

	
	//All of these conditions must be true to perform this command.
	function ConditionsMet(IRCMessage $ircmsg)
	{
		$result = false;
		//if ((strtoupper($ircmsg->GetTarget()) == strtoupper($this->bot->GetNick()) || $ircmsg->GetTarget() == $ircmsg->GetChannel()) && Command::MatchCommandString($ircmsg, $this->name))
		//if ((strtoupper($ircmsg->GetTarget()) === strtoupper($this->bot->GetNick())) && Command::MatchCommandString($ircmsg, $this->name)) //Only respond if targeted at me in priv/channelping and matches my command name.
		if ((strtoupper($ircmsg->GetTarget()) === strtoupper($this->bot->GetNick())) && Command::MatchCommandString($ircmsg, $this->name)) 
		{
			$result = true;
		}
		/*
		echo $ircmsg->GetTarget() .' == ' .$this->bot->GetNick() .' | ' .($ircmsg->GetTarget() == $this->bot->GetNick()) ."\n";
		echo $ircmsg->GetTarget() .' == ' .$ircmsg->GetChannel() .' | ' .($ircmsg->GetTarget() == $ircmsg->GetChannel()) ."\n";
		echo 'Command::MatchCommandString($ircmsg, $this->name)' .' | ' .(Command::MatchCommandString($ircmsg, $this->name)) ."\n";
		print_r($ircmsg);
		echo "$ | $result\n";
		*/		
		return $result;
	}

	
	//What to perform if the conditions are met.
	function Act(IRCMessage $ircmsg)
	{
		$result = '';
		$msgargs = $ircmsg->GetArgs();
		if (count($msgargs) > 0)
		{
			$result = $this->GetHelp($msgargs[0]);
		}
		else
		{
			$result .= $this->bot->GetHelp();
			$result .= "\nIf you need help with any of the other commands, say 'HELP CommandName'.";
		}
		return $result;
	}
	
	function GetHelp($cmdName)
	{
		$result = "Sorry, I'm not sure how $cmdName works...";
		$cmd = $this->bot->GetCommand($cmdName);
		if ($cmd)
		{
			$result = $cmd->Help();
		}
		return $result;
	}
	
	//Letting the user know how to use a command.
	function Help()
	{
		//Return a text description explaining the command's syntax and purpose.
		
		return "$this->name: $this->desc\n(Syntax: " .$this->bot->GetNick() .", $this->name [CommandName])";
	}

	
	//Accessors & Mutators
	
}