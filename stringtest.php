<?php
include 'message.php';

$string = "Omg this is the string PING :ANDSTUFF";
$replacement = "PONG";
echo "String: '$string'\n";
$regexp = "/PING /";
echo "Regexp: '$regexp'\n";
$result = preg_split($regexp, $string);
$c = count($result);
echo "Result: $replacement $result[1]\n Length: $c\n";
echo "String: '$string'\n";

function testFun()
{
	echo "OMG THIS IS A FUN: $string $replacement $regexp $c";
}

class exampleClass
{
	//Properties
	public $a;
	public $b;
	public $c;
	public $d;
	
	public function __construct()
	{
		$this->a;
		$this->b = 10;
		$this->c = "asdf";
		$this->d = "I have been assigned.";
	}
	
	public function display ()
	{
		echo "a: '$this->a'";
		echo "b: '$this->b'";
		echo "c: '$this->c'";
		echo "d: '$this->d'";
		echo "\n\n";
	}
	
	public function compRegExp($pattern, $subject)
	{
		$result = '';		
		$arr = preg_split($pattern, $subject);
		$c = count($arr);
		if ($c >= 2)
		{
			$result = $arr[1];
		}
		
		
		echo "Result: $arr[0] | $arr[1]\nLength: $c\n";
		//echo "String: '$string'\n";
		
		return $result;
	}
}

function testString($theString)
{
	$theMessage = new IRCMessage($theString);
	print_r($theMessage);
}

//testFun();
$cl = new exampleClass();
$cl->display();
$cl->compRegExp('/Retl!.*PRIVMSG Recreational_Pinkbot :/', ':Retl!smuxi@Pony-7463oe.ga.charter.com PRIVMSG Recreational_Pinkbot :QUIT');
$cl->compRegExp('/Retl!.*PRIVMSG Recreational_Pinkbot :/', ':Retl!smuxi@Pony-7463oe.ga.charter.com PRIVMSG Recreational_Pinkbot :QUIT');

$storage = [];

//Break the string into usermask, channel, and message.
$storage = explode(" ", ':Retl!smuxi@Pony-7463oe.ga.charter.com PRIVMSG Recreational_Pinkbot :QUIT'); 

$usermask = substr($storage[0], 1);
$channel = $storage[2];
$message = substr($storage[3], 1);

print_r($storage);

//Break the usermask into its component nick, userID, and host.
$storage = explode("!", $usermask); 

$nick = $storage[0];
$storage = explode("@", $storage[1]); 
$userID = $storage[0];
//$storage = preg_split('/.*@/', $storage[1]);
$host = $storage[1];


$otherresults = [$nick, $userID, $host, $channel, $message];
print_r($otherresults);

testString(':NICK!UserID@Pony.zz.dummy.hoof PRIVMSG Recreational_Pinkbot :QUIT');
testString(':Nick!UserID@dummy.interweb NOTICE Recreational_Pinkbot :*** End of Message(s) of the Day ***');
testString('no');
testString(':discord.canternet.org NOTICE Auth :*** Found your hostname (71-92-44-59.dhcp.athn.ga.charter.com) -- cached');

/*
:NICK!USERID@HOST PRIVMSG CHANNEL :MESSAGE
*/
