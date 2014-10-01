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
		$this->name = substr($theSuite,0,1) .$theValue;
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
}

class Deck
{
	//Properties
	private $contents = [];
	
	
	//Methods
	
	public function Shuffle()
	{
		//Shuffles the deck automatically.
	}
	
	public function RemoveCard($theCard)
	{
		//Remove a specific card from the deck and return it as the result.
		return array_splice($this->contents, $theCard, 1);
	}
	
	public function Deal($numCards)
	{
		//If there are enough cards in the deck, deal $numCards out of our deck and return them.
		$theDeal = [];
		for (; $numCards > 0 && $this->Count() > 0; $numCards--)
		{
			$randIndex = rand(0, $this->Count() - 1);
			$theDeal[] = $this->RemoveCard($randIndex);
		}
		return $theDeal;
	}
	
	public function AddCard($theCard)
	{
		//Adds a card to the end of the deck.
		$this->contents[] = $theCard;
	}
	
	public function Count()
	{
		return count($this->contents);
	}
}

class DefaultDeck extends Deck
{
	public function __construct()
	{
		$this->AddCard(new Card('Heart', 10));
		$this->AddCard(new Card('Spade', 10));
		$this->AddCard(new Card('Diamond', 10));
		$this->AddCard(new Card('Clubs', 10));
		$this->AddCard(new Card('Heart', 7));
		//parent::__construct();
	}
}