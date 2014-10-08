<?php
class Whiskey extends command
{
	//Properties
	protected $name = "Whiskey";
	protected $desc = "A selection of entertaining whiskey-related quotes. You may call me Blackbot or Botjack. Not Security, though. I lack incapacitation hardware.";
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
		//Only respond if targeted at me in priv/channelping and matches my command name and there are no arguments.
		{
			$result = true;
		}		
		return $result;
	}

	
	//What to perform if the conditions are met.
	function Act(IRCMessage $ircmsg)
	{
		$whiskeyReplies = ["ALL HAIL QUEEN WHISKEY!"];
		$whiskeyReplies[] = "No thanks, I'm good.";
		$whiskeyReplies[] = "No mixing. Wild Pegasus Only. FINAL DESTINATION.";
		$whiskeyReplies[] = "Oh rain may fall and the wind might blow, the earth could quake or clouds bury us in snow, but as bad as they are there's one thing I know... with friends and whiskey is how I plan to goooooo~";
		$whiskeyReplies[] = "I drink to your good health, good sir dragon.";
		$whiskeyReplies[] = "No. That is incorrect. I am drinking. More accurately, I am approaching the state of being that is drunk.";
		$whiskeyReplies[] = "My first drink went down like a Sparkle-Cola. :(";
		$whiskeyReplies[] = "~Oh you shoulda just sent the whiskey~";
		$whiskeyReplies[] = "~So best send me a whiskey!~";
		$whiskeyReplies[] = "Ooooo, gimmie!";
		$whiskeyReplies[] = "Whiskey mathematics says two shells equals three dead ponies!";
		$whiskeyReplies[] = "Oh thank you sweet merciful whiskey for you have taken the concussive beating that comes from hanging a few feet from a firing cannon muzzle and rendered it into a nice full-body numbness.";
		$whiskeyReplies[] = "Sunshine and whiskey! -I mean, Sunshine and Rainbows!";
		$whiskeyReplies[] = "We used all of it trying to sterilize you and the equipment anyway.";
		$whiskeyReplies[] = "so, which stable did you grow up in?";
		$whiskeyReplies[] = "My head is going around and around and whee~!";
		$whiskeyReplies[] = "No, but we do have Scotch Tape! ...We're gonna need a bigger bottle.";
		$whiskeyReplies[] = "I guess so that I wouldn't be lonely any more. Have a life like I did before the war.";
		$whiskeyReplies[] = "Aren't you a little young for that? I'm telling MoM!";
		$whiskeyReplies[] = "Can you mix it with a lollipop? Mmmmm~<3";
		return $whiskeyReplies[array_rand($whiskeyReplies)];
	}
	
	//Letting the user know how to use a command.
	function Help()
	{
		//Return a text description explaining the command's syntax and purpose.
		return "$this->name: $this->desc\n(Syntax: [" .$this->bot->GetNick() .",] $this->name)";
	}	
}