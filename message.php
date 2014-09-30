<?php
class IRCMessage
{
	//Properties
	
	private $storage;
	
	private $usermask;
	private $msgType;
	
	private $channel;
	private $nick;
	private $userID;
	private $host;
	private $message;
	private $cmd;
	
	public function __construct($msgString)
	{
		$result = NULL;
		if (!empty($msgString)) 
		{
			//Break the string into usermask, channel, and message.
			$this->storage = explode(" ", $msgString);
			if (count($this->storage) > 1)
			{
				$this->msgType = $this->storage[1];
			}
			
			
			//echo strpos($this->msgType, 'PRIVMSG') != false;
			/*
			echo count($this->storage) .'\n';
			$d = (strpos($this->msgType, 'PRIVMSG') !== false);
			echo $d .'\n';
			*/
			
			if (count($this->storage) > 1 && strpos($this->msgType, 'PRIVMSG') !== false)
			{
				$this->usermask = substr($this->storage[0], 1);
				$this->channel = $this->storage[2];
				//We have to take all of the remaining arguments back into one string, and trim the preceding colon.
				$this->cmd = substr($this->storage[3], 1);
				$this->args = array_slice($this->storage,4);
				$this->message = substr(implode(' ', array_slice($this->storage,3)), 1);

				//print_r($this->storage);

				//Break the usermask into its component nick, userID, and host.
				$this->storage = explode("!", $this->usermask); 

				$this->nick = $this->storage[0];
				$this->storage = explode("@", $this->storage[1]); 
				$this->userID = $this->storage[0];
				//$this->storage = preg_split('/.*@/', $this->storage[1]);
				$this->host = $this->storage[1];
				unset($this->storage);
				
				$result = $this;
			}
		}
		
		return $result;
	}
	
	public function GetChannel()
	{
		return $this->channel;
	}
	public function GetNick()
	{
		return $this->nick;
	}
	public function GetUserID()
	{
		return $this->userID;
	}
	public function GetHost()
	{
		return $this->host;
	}
	public function GetMessage()
	{
		return $this->message;
	}
	public function GetMessageType()
	{
		return $this->msgType;
	}
	public function GetCommand()
	{
		return $this->cmd;
	}
}
