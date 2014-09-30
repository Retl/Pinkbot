<?php
/*
Project Author: Carlis Moore
Project Name: Recreational Pinkbot FoE GM Assistant
Project Start Date:  28 Sept. 2014
Project Purpose: Track character data for the FoE PnP using commands issued directly from the irc.
*/

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
			$admincmd = $this->ParseAdminText($this->buf);
			//echo "admincmd = '$admincmd'";
			
			//And then do stuff.
			
			if (count($splitmsg = preg_split('/PING /', $this->buf)) > 1) 
			{
				$pongmsg = "PONG $splitmsg[1]\n";
				$this->Speak($pongmsg);
				//socket_write($this->sock, $pongmsg, strlen($pongmsg));
				//$this->Speak("Speaking now.");
			}
			if (count($splitmsg = preg_split('/SAY /', $admincmd)) > 1)
			{
				$this->Speak($splitmsg[1]);
			}
			if ($admincmd == '@@@join ballpit-ooc') 
			{
				$this->Speak('JOIN #ballpit-ooc');
			}
			if ($admincmd == 'dance') 
			{
				$this->Emote('does a little jig and pronks about merrily. Whee!~<3', $this->adminNick);
			}
			if ($admincmd == 'sing') 
			{
				$this->Speak('PRIVMSG Retl :Greetings.'); 
				$this->Speak("PRIVMSG $this->adminNick :Hello!");
				$this->Speak('Hi');
				$this->Speak('squeakysqueakings!', 'Retl');
				$this->Speak('When I was a little filly and the Sun was going Do~wn~', $this->adminNick);
			}
			if ($this->buf == 'quit' || $admincmd == 'QUIT') 
			{
				$this->Speak("QUIT :Returning to Burst Station 7. POP!");
				break;
			}
			if ($this->buf == 'shutdown') 
			{
				socket_close($this->sock);
				break;
			}
			echo "$this->buf\n";
			
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
	
	function Emote($speakMe, $destination = '')
	{
		$pre = chr(1);
		$pre .= "ACTION ";
		$speakMe .=  chr(1);
		$this->Speak("$pre$speakMe", $destination);
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
		
	
	
	
