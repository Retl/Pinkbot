<?php
include 'message.php';
include 'card.php';
include 'blackjack.php';



$acard = new Card('Diamond', 1);
$theDeck = new DefaultDeck();
echo 'The deck contains ' .$theDeck->Count() ." cards. \n";
echo "The deck's cards: [$theDeck]";
echo "\n----------{True and False}----------\n";
$trashme = new Player("Nameless");
echo "Turn Over?: [" .$trashme->IsTurnOver() ."]";
$trashme->SetTurnOver(false);
echo "Turn Over?: [" .$trashme->IsTurnOver() ."]";
$trashme->SetTurnOver(true);
echo "Turn Over?: [" .$trashme->IsTurnOver() ."]";

echo true === false; //Apparently, false when evaluated to a string is a blank character. Good to know.
echo false === false;

echo "\n----------{BLACKJACK}----------\n";
$testPlayer = new Player("Tester1");
$testPlayer2 = new Player("Tester2");
$testBJ = new Blackjack('TestChannel', [$testPlayer], $testPlayer2);
$testBJ->StartSession();
$testBJ->Hit($testPlayer);
$testBJ->Stay($testPlayer);
$testBJ->Stay($testPlayer);
//print_r($testBJ);

/*
$theDeck->AddCard($acard);
print_r($theDeck);
print_r($acard);

print_r($theDeck->Deal(3));
print_r($theDeck);
*/