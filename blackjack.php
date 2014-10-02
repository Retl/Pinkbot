<?php
class Blackjack
{
	//Properties
	
	private $sessionID; //If two different groups of people are playing at the same time, don't mix them up.
	private $players; //All players participating. They should have specific nicks and their own hands.
	private $house; //Kinda like a special case of player which refers the one running the game.
	private $deck;
	private $gameOver = false;
	const BUSTLIMIT = 21;
	const HIGHBONUS = 10;
	const TESTING = true;
	const HOUSEMINIMUM = 17;
	
	
	public function __construct($theSID, $thePlayers = [], $theHouse)
	{
		$this->sessionID = $theSID;
		$this->players = $thePlayers;		
		$this->deck = new DefaultDeck();
		
		if ($theHouse === NULL)
		{
			$theHouse = new Player("Default_House");
		}
		$this->house = $theHouse;
		
		$this->players[] = $this->house; //The house is also a player, though their role is automated.
		
		
	}
	
	public function Hit($player)
	{
		if (!$this->IsGameOver())
		{
			$busted = $this->IsBusted($player->GetHand());
			if (!$player->IsTurnOver() && !$busted)
			{
				$player->GetHand()->AddCard($this->deck->DrawCard());
				$player->SetTurnOver(true);
			}
			
			if ($busted)
			{
				$player->SetPlaying(false);
			}
			else
			{
				$player->SetPlaying(true);
			}
			
			$this->AdvanceIfRoundOver();
		}		
	}
	
	public function Stay($player)
	{
		if (!$this->IsGameOver())
		{
			$player->SetTurnOver(true);
			$player->SetPlaying(false);
			$this->AdvanceIfRoundOver();
		}		
	}
	
	public function IsBusted($hand)
	{
		$result = false;
		if ($this->CalculateScore($hand) > $this::BUSTLIMIT)
		{
			$result = true;
		}
		return $result;
	}
	
	public function CalculateScore($hand, $useHigh = true)
	{
		$result = 0;
		for ($i = 0; $i < $hand->Count(); $i++)
		{
			$cur = $hand->GetCard($i);
			if (!$cur)
			{
				$this->TestingEcho("\$cur is evaluating as a null again: [$cur]\n");				
			}
			
			//$this->TestingEcho("\$hand: [$hand]\n");
			
			if ($cur->GetValue() === 1)
			{
				if (!$useHigh)
				{
					$result += $cur->GetValue();
				}
				else
				{
					if ($result + $cur->GetValue() > $this::BUSTLIMIT)
					{
						//If taking the high would make you bust, take the low.
						$result += $cur->GetValue();
					}
					else
					{
						//Otherwise, take the high.
						$result += $cur->GetValue() + $this::HIGHBONUS;
					}
				}
			}
			else
			{
				$temp = $cur->GetValue();
				
				//Face cards other than Ace are all worth 10 points.
				if ($temp > 10)
				{
					$temp = 10;
				}
				$result += $temp;
			}
		}
		//echo $result ."-";
		$this->TestingEcho("\$hand Value: [$result]\n");
		return $result;
	}
	
	public function IsAnyonePlaying()
	{
		$result = false;
		for ($i = 0; $i < count($this->players) && $result != true; $i++)
		{
			if ($this->players[$i]->IsPlaying())
			{
				$result = true;
			}
		}
		return $result;
	}
	
	public function EndIfNoonePlaying()
	{
		if (!$this->IsAnyonePlaying())
		{
			$this->EndSession();
		}
	}
	
	public function AdvanceIfRoundOver()
	{
		if ($this->IsRoundOver())
		{
			if ($this->IsAnyonePlaying())
			{
				$this->StartRound();
			}
			else
			{
				$this->EndSession();
			}
		}
	}
	
	public function IsRoundOver()
	{
		$result = true;
		for ($i = 0; $i < count($this->players); $i++)
		{
			if (!$this->players[$i]->IsTurnOver())
			{
				$result = false;
				$this->TestingEcho("Players[$i]'s turn is not over!\n");
				break;
			}
		}
		if ($result === true)
		{
			$this->TestingEcho("TURN OVER.\n");
		}
		return $result;
	}
		
	public function IsGameOver()
	{
		return $this->gameOver;
	}
	
	public function StartRound()
	{
		$this->TestingEcho("--{New Round Start}--\n");
		//All players are now able to take a turn.
		for ($i = 0; $i < count($this->players); $i++)
		{
			$this->players[$i]->SetTurnOver(false);
		}
		
		//House goes first.
		//If the house is not up to HOUSEMINIMUM (17), they must hit.
		$houseHand = $this->house->GetHand();
		if (!$this->IsBusted($houseHand) && $this->CalculateScore($houseHand) < $this::HOUSEMINIMUM)
		{
			$this->TestingEcho("House is less than 17 and therefore must hit.\n");
			$this->Hit($this->house);
		}
		else
		{
			$this->TestingEcho("House has exceeded 17 and must STAY.\n");
			$this->Stay($this->house);
		}
	}
	
	public function StartSession()
	{
		/*
		function StartingDraw($player)
		{
			$player->GetHand()->AddCard($this->deck->DrawCard());
			$player->GetHand()->AddCard($this->deck->DrawCard());
		}
		*/
		//array_walk($this->players, 'StartingDraw'); //I have no freaking clue how to make this work and don't care to fix it right now.
		
		for ($i = 0; $i < count($this->players); $i++)
		{
			$player = $this->players[$i];
			$player->GetHand()->AddCard($this->deck->DrawCard());
			$player->GetHand()->AddCard($this->deck->DrawCard());
		}
		
		$this->StartRound();
	}
	
	public function ResetSession()
	{
		
	}
	
	public function EndSession()
	{
		$this->gameOver = true;
		$result = "-----{GAME OVER}-----\n";
		$this->TestingEcho("-----{GAME OVER}-----\n");
		
		$houseHand = $this->house->GetHand();
		$houseNick = $this->house->GetNick();
		$houseScore = $this->CalculateScore($houseHand);
		if ($houseScore > $this::BUSTLIMIT)
		{
			$result .= $this->TestingEcho("$houseNick went bust with a score of $houseScore [$houseHand]\n");
		}
		else
		{
			$result .= $this->TestingEcho("$houseNick scored $houseScore [$houseHand]\n");
		}
		
		for ($i = 0; $i < count($this->players); $i++)
		{
			$playerHand = $this->players[$i]->GetHand();
			$playerScore = $this->CalculateScore($playerHand);
			$playerNick = $this->players[$i]->GetNick();
			if ($playerScore > $this::BUSTLIMIT)
			{
				$result .= $this->TestingEcho("$playerNick went bust with a score of $playerScore [$playerHand]\n");
			}
			else
			{
				$result .= $this->TestingEcho("$playerNick scored $playerScore [$playerHand]\n");
			}
		}
		return $result;
	}
	
	public function TestingEcho($out)
	{
		if ($this::TESTING) {echo $out;}
		return $out;
	}
}

class Player
{
	//Properties
	private $nick;
	private $hand;
	private $usedTurn;
	private $playing;
	
	//Methods
	public function __construct($theNick)
	{
		$this->nick = $theNick;
		$this->hand = new Deck();
		$this->usedTurn = false;
		$this->playing = true;
	}
	
	public function GetNick()
	{
		return $this->nick;
	}
	
	public function GetHand()
	{
		return $this->hand;
	}
	
	public function IsPlaying()
	{
		return $this->playing;
	}
	
	public function SetPlaying($p = true)
	{
		$this->playing = $p;
	}
	
	public function IsTurnOver()
	{
		return $this->usedTurn;
	}
	
	public function SetTurnOver($t)
	{
		$this->usedTurn = $t;
	}
	
}