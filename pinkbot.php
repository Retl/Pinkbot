<?php
/*
Project Author: Carlis Moore
Project Name: Recreational Pinkbot FoE GM Assistant
Project Start Date:  28 Sept. 2014
Project Purpose: Track character data for the FoE PnP using commands issued directly from the irc.
*/

include 'message.php';
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
			
			if (count($splitmsg = preg_split('/PING /', $this->buf)) > 1) 
			{
				$pongmsg = "PONG $splitmsg[1]\n";
				$this->Speak($pongmsg);
				//socket_write($this->sock, $pongmsg, strlen($pongmsg));
				//$this->Speak("Speaking now.");
			}
			if ($ircmsg->GetNick() === $this->adminNick && $this->MatchCommandString($ircmsg, 'SAY'))
			{
				$this->Speak($ircmsg->GetMessage());
			}
			//Help command.
			if ($this->MatchCommandString($ircmsg, 'HELP')) 
			{
				$this->Help($ircmsg);
			}
			if ($this->MatchCommandString($ircmsg, 'Dice'))
			{
				$this->Reply($ircmsg, "Rolling dice is easy! Just tell me how many you want and how many sides they should have. Try \"1d20\".");
			}
			if ($this->MatchCommandString($ircmsg, 'JOIN')) 
			{
				/*
				$joinstring = substr($ircmsg->GetMessage(), 5);
				$this->Speak("JOIN $joinstring");
				*/
				$this->Speak($ircmsg->GetMessage());
			}
			if ($this->MatchCommandString($ircmsg, 'ECHO') || $this->MatchCommandString($ircmsg, 'Mirror'))
			{
				$this->Mirror($ircmsg);
			}
			if ($this->MatchCommandString($ircmsg, 'DANCE')) 
			{
				$this->ReplyEmote($ircmsg, 'does a little jig and pronks about merrily. Whee!~<3');
			}
			/*
			if ($admincmd == 'dance') 
			{
				$this->Emote('does a little jig and pronks about merrily. Whee!~<3', $this->adminNick);
			}
			*/
			if ($this->MatchCommandString($ircmsg, 'SING'))
			{
				$this->Reply($ircmsg, 'When I was a little filly and the Sun was going Do~wn~');
			}
			
			if ($this->MatchCommandString($ircmsg, 'HI'))
			{
				$this->Reply($ircmsg, "Hiiii there~! Let's play!");
			}
			if ($this->MatchCommandString($ircmsg, 'Whiskey'))
			{
				$this->QueenWhiskey($ircmsg);
			}
			if ($this->MatchCommandString($ircmsg, 'Blackjack'))
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
			if ($this->MatchCommandString($ircmsg, 'Hit'))
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
			if ($this->MatchCommandString($ircmsg, 'Stay'))
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
			
			//Dieroller.
			if($this->MatchCommandStringRegexp($ircmsg, '/^\d+[dD]\d+/'))
			{
				//Consider adding a variant or argument that allows for exploding rolls?
				$explodedInput = explode("D", strtoupper($ircmsg->GetCommand()));
				$numDie = $explodedInput[0];
				$numSides = $explodedInput[1];
				$roll = [];
				$sum = 0;
				
				for ($temp = $numDie; $temp > 0; $temp--)
				{
					$currentRoll = rand(1, $numSides);
					$roll[] = $currentRoll;
					$sum += $currentRoll;
				}
				
				$this->Reply($ircmsg, $ircmsg->GetCommand() .": $sum = [" .implode(" + ", $roll) ."]");
			}
			if ($ircmsg->GetNick() === $this->adminNick && $this->MatchCommandString($ircmsg, 'QUIT')) 
			{
				$this->Reply($ircmsg, "Okie doki loki! Later!");
				$this->Quit();
				break;
			}
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
		else
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
	
	public function QueenWhiskey($ircmsg)
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

		$this->Reply($ircmsg, $whiskeyReplies[array_rand($whiskeyReplies)]);
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
	
}

$tries = 5;

ob_implicit_flush();

$thePinkbot = new Pinkbot();
$thePinkbot->Main();



	
    $msg = "\nWelcome to the PHP Test Server. \n" .
        "To quit, type 'quit'. To shut down the server type 'shutdown'.\n";
		
	
	
	
