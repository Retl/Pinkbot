<?php
class Card
{
	//Properties
	
	private $name;
	private $value;
	private $suite;
	
	
	public function __construct($theSuite, $theValue)
	{
		$this->suite = $theSuite;
		$this->value = $theValue;
		
		$firstChar = substr($theSuite,0,1);
		
		//Oops! Seems like this text doesn't display well, but if you have something that can handle it, just uncomment this block to get it back.
		/*
		switch ($this->suite)
		{
			case "Club": 
			$firstChar = '♣';
			break;
			
			case "Diamond": 
			$firstChar = '♦';
			break;
			
			case "Heart": 
			$firstChar = '♥';
			break;
			
			case "Spade": 
			$firstChar = '♠';
			break;
			
			default:
			break;
		}
		*/
		
		switch ($this->value)
		{
			case 1: 
			$nval = 'A';
			break;
		
			case 11: 
			$nval = 'J';
			break;
			
			case 12: 
			$nval = 'Q';
			break;
			
			case 13: 
			$nval = 'K';
			break;
			
			default:
			$nval = $this->value;
			break;
		}
		
		$this->name = $firstChar .$nval;
	}
	
	public function GetName()
	{
		return $this->name;
	}
	
	public function GetValue()
	{
		return $this->value;
	}
	
	public function GetSuite()
	{
		return $this->suite;
	}
	
	public function __toString()
	{
		return $this->GetName();
	}
}

class Deck
{
	//Properties
	private $contents = [];
	
	
	//Methods
	
	public function Shuffle()
	{
		shuffle($this->contents);
	}
	
	public function DrawCard($theCard = 0)
	{
		//Remove a specific card from the deck and return it as the result.
		$result = NULL;
		if (count($this->contents) > 0)
		{
			$result = array_splice($this->contents, $theCard, 1)[0];
		}
		return $result;
	}
	
	public function Deal($numCards = 1)
	{
		//If there are enough cards in the deck, deal $numCards out of our deck and return them.
		$theDeal = [];
		for (; $numCards > 0 && $this->Count() > 0; $numCards--)
		{
			$randIndex = rand(0, $this->Count() - 1);
			$theDeal[] = $this->DrawCard($randIndex);
		}
		return $theDeal;
	}
	
	public function AddCard($theCard)
	{
		//Adds a card to the end of the deck.
		$this->contents[] = $theCard;
	}
	
	public function GetCard($theCard)
	{
		//Gets a reference to a specific card at an index.
		return $this->contents[$theCard];
	}
	
	public function Count()
	{
		return count($this->contents);
	}
	
	public function __toString()
	{
		$result = '';
		$cardList = [];
		for ($i = 0; $i < $this->Count(); $i++)
		{
			$cardList[] = $this->GetCard($i)->GetName();
		}
		
		$result = implode(' ', $cardList);
		return $result;
	}
}

class DefaultDeck extends Deck
{
	//The default deck is the usual 52 card French deck.
	public function __construct()
	{
		for ($i = 0; $i < 4; $i++)
		{
			switch ($i)
			{
				case 0: 
				$tempSuite = "Club";
				break;
				
				case 1: 
				$tempSuite = "Diamond";
				break;
				
				case 2: 
				$tempSuite = "Heart";
				break;
				
				case 3: 
				$tempSuite = "Spade";
				break;
			}
			
			for ($j = 1; $j < 14; $j++)
			{
				$this->AddCard(new Card($tempSuite, $j));
			}
		}
		$this->Shuffle();
		//parent::__construct();
	}
}