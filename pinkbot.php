<?php
/*
Project Author: Carlis Moore
Project Name: Recreational Pinkbot FoE GM Assistant
Project Start Date:  28 Sept. 2014
Project Purpose: Track character data for the FoE PnP using commands issued directly from the irc.
*/

include 'message.php';
include 'command.php';
include '\commands\help.php';
include '\commands\pong.php';
include '\commands\whiskey.php';
include '\commands\dice.php';
include '\commands\quit.php';
include 'card.php';
include 'blackjack.php';

class Pinkbot
{
	//Properties
	public $nick;
	public $adminNick;

	public $hostname;
	public $address;
	public $port;
	public $sock;
	
	public $gameData;
	
	public $commandList;
	
	function __construct()
	{
		$this->nick = 'Recreational_Pinkbot';
		echo "~~>@ $this->nick initializing. @<~~\n";
		$this->adminNick = 'Retl';
		echo "~~>@ $this->nick registering admin's nick. @<~~\n";
		$this->hostname = 'irc.canternet.org';
		echo "Hostname: '$this->hostname' \n";
		$this->address = gethostbyname($this->hostname);
		echo "Address: '$this->address' \n";
		$this->port = 6667;
		echo "Port: '$this->port' \n";
		
		//Make a dictionary of all of the commands that we'll be using.
		$tempcmds = [new Pong($this), new Help($this), new Whiskey($this),
		new Dice($this), new Quit($this)];
		/*
		echo "Name: " .$tempcmds[0]->GetName();
		echo "Name: " .$tempcmds[1]->GetName();
		print_r($tempcmds);
		*/
		foreach($tempcmds as $com)
		{
			$this->commandList[strtoupper($com->GetName())] = $com;
		}
		//print_r($this->commandList);
		arsort($this->commandList);
		unset($tempcmds);
		
	}
	
	//Methods
	function Main()
	{
		if (($this->sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) === false) 
		{
			echo "socket_create() failed: reason: " . socket_strerror(socket_last_error()) . "\n";
		}
		else
		{
			echo "Socket created successfully.";
		}

		echo "Attempting to connect to '$this->hostname':'$this->port'...";
		if (socket_connect($this->sock, $this->address, $this->port) === false) 
		{
			echo "socket_bind() failed: reason: " . socket_strerror(socket_last_error($sock)) . "\n";
		}
		else
		{
			echo "Socket connected successfully.\n";
		}

		$welcomeMessage = "NICK $this->nick\n";
		$welcomeMessage .= "USER $this->nick 8 * : Recreational Pinkbot\n";
		
		//Join the usual channels...
		$this->speak("JOIN #ballpitooc");
		//Test the RequestNickVerification.
		$this->RequestNickVerification("Retl");

		socket_write($this->sock, $welcomeMessage, strlen($welcomeMessage));
		
		while (true) 
		{
			//$tries -= 1;
			//echo "Attempts remaining: $tries\n";
			
			//Get the text from the socket to the buffer.
			if (false === ($this->buf = socket_read($this->sock, 2048, PHP_NORMAL_READ))) 
			{
				echo "socket_read() failed: reason: " . socket_strerror(socket_last_error($this->sock)) . "\n";
				break;
			}
			
			//Trim the space.
			if (!$this->buf = trim($this->buf)) 
			{
				continue;
			}
			
			//Parse the buffered text.
			$ircmsg = new IRCMessage($this->buf);
			$admincmd = $this->ParseAdminText($this->buf);
			
			//Display the buffer in the console window.
			echo "$this->buf\n";
			
			//echo "admincmd = '$admincmd'";
			
			//And then do stuff.
			/*
			if ($ircmsg->GetNick() === $this->adminNick && Command::MatchCommandString($ircmsg, 'SAY'))
			{
				$this->Speak($ircmsg->GetMessage());
			}
			*/
			if (Command::MatchCommandString($ircmsg, 'JOIN')) 
			{
				/*
				$joinstring = substr($ircmsg->GetMessage(), 5);
				$this->Speak("JOIN $joinstring");
				*/
				$this->Speak($ircmsg->GetMessage());
			}
			/*
			if (Command::MatchCommandString($ircmsg, 'ECHO') || Command::MatchCommandString($ircmsg, 'Mirror'))
			{
				$this->Mirror($ircmsg);
			}
			if (Command::MatchCommandString($ircmsg, 'DANCE')) 
			{
				$this->ReplyEmote($ircmsg, 'does a little jig and pronks about merrily. Whee!~<3');
			}
			*/
			/*
			if ($admincmd == 'dance') 
			{
				$this->Emote('does a little jig and pronks about merrily. Whee!~<3', $this->adminNick);
			}
			*/
			/*
			if (Command::MatchCommandString($ircmsg, 'SING'))
			{
				$this->Reply($ircmsg, 'When I was a little filly and the Sun was going Do~wn~');
			}
			
			if (Command::MatchCommandString($ircmsg, 'HI'))
			{
				$this->Reply($ircmsg, "Hiiii there~! Let's play!");
			}
			*/
			if (Command::MatchCommandString($ircmsg, 'Blackjack'))
			{
				$house = $this->gameData['house'.$ircmsg->GetChannel().$ircmsg->GetNick()] = new Player("House");
				$player = $this->gameData['player'.$ircmsg->GetChannel().$ircmsg->GetNick()] = new Player($ircmsg->GetNick());
				$game = $this->gameData['blackJack'.$ircmsg->GetChannel().$ircmsg->GetNick()] = new Blackjack($ircmsg->GetChannel(), [$player], $house);
				
				/*
				$house = new Player("House");
				$player = new Player($ircmsg->GetNick());
				$testBJ = new Blackjack($ircmsg->GetChannel(), [$testPlayer], $House);
				*/
				
				$whiskeyReplies = ["I'll make your own game! With Blackjack! And Hookers! ...Actually, forget the hookers!"];
				$whiskeyReplies[] = "Yay, a game! I love games! Let's play!";
				$whiskeyReplies[] = "Okie doki loki! One round of Blackjack on the house coming right up!";
				$this->Reply($ircmsg, $whiskeyReplies[array_rand($whiskeyReplies)]);
				
				$game->StartSession();
				$this->Reply($ircmsg, "Your hand: [" .$player->GetHand().']');
				$this->Reply($ircmsg, $house->GetNick() ."'s hand: [" .$house->GetHand().']');
				
			}
			if (Command::MatchCommandString($ircmsg, 'Hit'))
			{
				$house = $this->gameData['house'.$ircmsg->GetChannel().$ircmsg->GetNick()];
				$game = $this->gameData['blackJack'.$ircmsg->GetChannel().$ircmsg->GetNick()];
				$player = $this->gameData['player'.$ircmsg->GetChannel().$ircmsg->GetNick()];
				if ($house === null || $player === null || $game === null)
				{
					$this->Reply($ircmsg, "We're not playing anything yet, silly! If you wanna play Blackjack say BLACKJACK!");
				}
				else
				{
					$game->Hit($player);
					if ($game->IsGameOver())
					{
						//$endingMsgs = $game->EndSession();
						/*
						for ($i = 0; $i < count($endingMsgs); $i++)
						{
							$this->Reply($ircmsg, $endingMsgs[$i]);
						}
						*/
						
						//From this marker...
						$playerNick = $player->GetNick();
						$playerHand = $player->GetHand();
						$playerScore = $game->CalculateScore($playerHand, true);
						if ($playerScore > $game::BUSTLIMIT) 
						{
							$playerScore = $game->CalculateScore($playerHand, false);
						}
						$this->Reply($ircmsg, "$playerNick scored $playerScore [$playerHand]");
						
						$houseNick = $house->GetNick();
						$houseHand = $house->GetHand();
						$houseScore = $game->CalculateScore($houseHand, true);
						if ($houseScore > $game::BUSTLIMIT) 
						{
							$houseScore = $game->CalculateScore($houseHand, false);
						}
						$this->Reply($ircmsg, "$houseNick scored $houseScore [$houseHand]");
						// ...To this marker, we have to do a lot of repetitive crap in Stay. Refactor this.
					}
					else
					{
						$this->Reply($ircmsg, "Your hand: [" .$player->GetHand().']');
						$this->Reply($ircmsg, $house->GetNick() ."'s hand: [" .$house->GetHand().']');
					}
				}
			}
			if (Command::MatchCommandString($ircmsg, 'Stay'))
			{
				$house = $this->gameData['house'.$ircmsg->GetChannel().$ircmsg->GetNick()];
				$game = $this->gameData['blackJack'.$ircmsg->GetChannel().$ircmsg->GetNick()];
				$player = $this->gameData['player'.$ircmsg->GetChannel().$ircmsg->GetNick()];
				if ($house === null || $player === null || $game === null)
				{
					$this->Reply($ircmsg, "We're not playing anything yet, silly! If you wanna play Blackjack say BLACKJACK!");
				}
				else
				{
					$game->Stay($player);
					if ($game->IsGameOver())
					{
						$playerNick = $player->GetNick();
						$playerHand = $player->GetHand();
						$playerScore = $game->CalculateScore($playerHand, true);
						if ($playerScore > $game::BUSTLIMIT) 
						{
							$playerScore = $game->CalculateScore($playerHand, false);
						}
						$this->Reply($ircmsg, "$playerNick scored $playerScore [$playerHand]");
						
						$houseNick = $house->GetNick();
						$houseHand = $house->GetHand();
						$houseScore = $game->CalculateScore($houseHand, true);
						if ($houseScore > $game::BUSTLIMIT) 
						{
							$houseScore = $game->CalculateScore($houseHand, false);
						}
						$this->Reply($ircmsg, "$houseNick scored $houseScore [$houseHand]");
					}
					else
					{
						$this->Reply($ircmsg, "Your hand: [" .$player->GetHand().']');
						$this->Reply($ircmsg, $house->GetNick() ."'s hand: [" .$house->GetHand().']');
					}
				}
			}
			if ($ircmsg->GetNick() === $this->adminNick && Command::MatchCommandString($ircmsg, 'QUIT')) 
			{
				$this->Reply($ircmsg, "Okie doki loki! Later!");
				$this->Quit();
				break;
			}
			//Iterate through all of the known commands, check its conditions, and if its conditions are met, perform its actions.
			foreach ($this->commandList as $currentCommand)
			{
				if ($currentCommand->ConditionsMet($ircmsg))
				{
					$outString = $currentCommand->Act($ircmsg);
					$a = explode("\n", $outString);
					foreach ($a as $mes)
					{
						$this->reply($ircmsg, $mes);
					}
					break;
				}
			}
			
			//Finally, check for the shutdown trigger. If this shows up, we shutdown immediately.
			if ($this->buf == 'shutdown') 
			{
				socket_close($this->sock);
				break;
			}
			
		}
		socket_close($this->sock);
		
	}
	
	function Speak($speakMe, $destination = '')
	{
		$pre = '';
		
		if ($destination != '' && $destination != NULL)
		{
			$pre = "PRIVMSG $destination :";
		}
		
		$speakMe .= "\n";
		
		$result = $pre;
		$result .= $speakMe;
		
		echo "$result";
		
		socket_write($this->sock, $result, strlen($result));
	}
	
	public function Emote($speakMe, $destination = '')
	{
		//This whole sandwiching thing could be made into its own method, as we also use it in EmoteReply.
		$pre = chr(1);
		$pre .= "ACTION ";
		$speakMe .=  chr(1);
		$this->Speak("$pre$speakMe", $destination);
	}
	
	public function Reply($ircmsg, $response)
	{
		if ($ircmsg->GetChannel() === $this->nick)
		{
			//The private message to me, so I would see the channel as being my own nick.
			//Reply with the speaker's nick as the channel.
			$this->Speak($response, $ircmsg->GetNick());
		}
		else if (!$ircmsg->GetNick()) //A rare IRC command that doesn't have an obvious source is probably from the server, and doesn't need the nick reference.
		{
			$this->Speak($response);
		}
		else //Reply to a specific user.
		{
			$this->Speak($ircmsg->GetNick() .', ' .$response, $ircmsg->GetChannel());
		}
	}
	
	public function ReplyEmote($ircmsg, $response)
	{
		$pre = chr(1);
		$pre .= "ACTION ";
		$response .=  chr(1);
		
		//Rather than just passing it on to reply, we do our own checks here, and ignore the username portion in the reply. 
		if ($ircmsg->GetChannel() === $this->nick)
		{
			//The private message to me, so I would see the channel as being my own nick.
			//Reply with the speaker's nick as the channel.
			$this->Speak("$pre$response", $ircmsg->GetNick());
		}
		else
		{
			$this->Speak("$pre$response", $ircmsg->GetChannel());
		}
	}
	
	public function Help($ircmsg)
	{
		$help = "My available commands are: Dieroll (Format: #d#), Dance, Hi, Join (Join #NameOfChannel), Sing, Whiskey, and QUIT (Admin Only).";
		$this->Reply($ircmsg, $help);
	}
	
	public function Quit()
	{
		$this->Speak("QUIT :Returning to Burst Station " .rand(1,7) .". >POP!<");
	}
	
	public function Mirror($ircmsg)
	{
		$this->Reply($ircmsg, $ircmsg->GetMessage());
	}
	
	public function MatchCommandString($ircmsg, $cmd)
	{
		$result = false;
		if (strtoupper($ircmsg->GetCommand()) === strtoupper($cmd))
		{
			$result = true;
		}
		return $result;
	}
	
	public function MatchCommandStringRegexp($ircmsg, $pattern)
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
	
	public function SearchText($pattern, $subject)
	{
		$result = '';
		$arr = preg_split($pattern, $subject);
		$c = count($arr);
		if ($c >= 2)
		{
			$result = $arr[1];
		}
		//echo "Admin Text Result: '$result' $pattern $subject";
		return $result;
	}
	
	public function ParseAdminText($subject)
	{
		return $this->SearchText("/$this->adminNick!.*PRIVMSG $this->nick :/", $subject);
	}
	
	public function RequestNickVerification($theNick)
	{
		$this->Speak("ACC $theNick", NickServ);
		$this->Speak("WHOIS $theNick", NickServ);
		//Put an entry in a queue in the bot to wait for this ACC response. Once you get it, set its response value to correspond.
	}
	
	public function SetNickVerificationLevel($theNick, $rank)
	{
		//If you get a response to a NickVerification Request, pair the two.
		
		/*
		The answer is in the form <nick> ACC <digit>:
		NickServ	    0 - account or user does not exist
		NickServ	    1 - account exists but user is not logged in
		NickServ	    2 - user is not logged in but recognized (see ACCESS)
		NickServ	    3 - user is logged in
		*/
	}
	
	public function GetNick()
	{
		return $this->nick;
	}
	
	public function GetAdminNick()
	{
		return $this->adminNick;
	}
	
	//Gets a specific command object from the commandList array and returns it.
	public function GetCommand($cmdName)
	{
		$cmdName = strtoupper($cmdName);
		$result;
		if (isset($this->commandList[$cmdName]))
		{
			$result = $this->commandList[$cmdName];
		}
		else
		{
			$result = NULL;
		}
		return $result;
	}
	
	public function GetHelp()
	{
		$commandListString = '';
		foreach ($this->commandList as $com) {$commandListString .= $com->GetName() .', ';}
		$commandListString = substr($commandListString, 0, -2);
		$result = "Hello! The joint efforts of Robronco and Ministry of Morale have allowed me to help improve your super funtabulous playtime experience! The commands I recognize are: [$commandListString]. You can get help on any of those by saying 'HELP [Commandname].'";
		return $result;
	}
	
}

$tries = 5;

ob_implicit_flush();

$thePinkbot = new Pinkbot();
$thePinkbot->Main();



	
    $msg = "\nWelcome to the PHP Test Server. \n" .
        "To quit, type 'quit'. To shut down the server type 'shutdown'.\n";
		
	
	
	
