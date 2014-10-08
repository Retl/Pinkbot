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
	private $args;
	
	private $target;
	
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
			
			//Special case for detecting PINGs.
			if ($this->storage[0] == "PING")
			{
				$this->msgType = $this->storage[0];
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
				
				//The moment we have the cmd, check to see if it is phrased in a way to direct it at a user. If so, store that nick and get the next arg.
				$offset = 0;
				if (strrchr($this->cmd, ',') == ','|| strrchr($this->cmd, ':') == ':')
				{
					$this->target = substr($this->cmd, 0, -1);
					$offset++; //Make sure that for the following args and message, you SKIP the user it's directed at, and assume that the command was at its usual spot.
					$this->cmd = $this->storage[$offset + 3]; //Don't trim this time. We already ate the commanding colon character.
				}
				else
				{
					//If we don't see the usual , or : indication of responding to a user, we can assume they are referring to the channel in general.
					$this->target = $this->channel;
				}
				
				//Assign the rest of the stuffs.
				
				$this->args = array_slice($this->storage,$offset + 4);
				$this->message = implode(' ', array_slice($this->storage,$offset + 3));
				
				//We should only trim the leading character if we didn't need the offset.
				if ($offset <= 0)
				{
					$this->message = substr($this->message, 1);
				}
				
				//unset($offset);

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
			else if ($this->msgType == "PING")
			{
				$this->cmd = "PONG";
				$this->args = array_slice($this->storage, 1);
				$this->message = implode('', array_slice($this->storage, 1));
				
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
	public function GetArgs()
	{
		return $this->args;
	}
	public function GetTarget()
	{
		return $this->target;
	}
}
