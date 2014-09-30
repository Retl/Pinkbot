<?php
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

testFun();
$cl = new exampleClass();
$cl->display();
$cl->compRegExp('/Retl!.*PRIVMSG Recreational_Pinkbot :/', ':Retl!smuxi@Pony-7463oe.ga.charter.com PRIVMSG Recreational_Pinkbot :QUIT');
$cl->compRegExp('/Retl!.*PRIVMSG Recreational_Pinkbot :/', ':Retl!smuxi@Pony-7463oe.ga.charter.com PRIVMSG Recreational_Pinkbot :QUIT');

/*
:cadance.canternet.org 353 Recreational_Pinkbot = #ballpit-ooc :Recreational_Pin
kbot @Retl
:cadance.canternet.org 366 Recreational_Pinkbot #ballpit-ooc :End of /NAMES list
.
:Retl!smuxi@Pony-7463oe.ga.charter.com PRIVMSG #ballpit-ooc :Sweet
:Retl!smuxi@Pony-7463oe.ga.charter.com PRIVMSG #ballpit-ooc :Okay now I know how
 to check which channel it's getting the messages from
 :Retl!smuxi@Pony-7463oe.ga.charter.com PRIVMSG #ballpit-ooc :☺ACTION does a litt
le jig☺
*/
