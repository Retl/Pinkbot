<?php
/*
Project Author: Carlis Moore
Project Name: Recreational Pinkbot FoE GM Assistant
Project Start Date:  28 Sept. 2014
Project Purpose: Track character data for the FoE PnP using commands issued directly from the irc.
*/

include 'message.php';

class Pinkbot
{
	//Properties
	public $nick;
	public $adminNick;

	public $hostname;
	public $address;
	public $port;
	public $sock;
	
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
		$help = "My available commands are: Dieroll (Format: #d#), Dance, Hi, Join (Join #NameOfChannel), Sing, and QUIT (Admin Only).";
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
		$this->Reply($ircmsg, array_rand($whiskeyReplies));
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
		
	
	
	
