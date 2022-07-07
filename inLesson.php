<?php
class Element{
    private string $elementName;
    private array $winsOver = [];
    public function __construct(string $elementName){
        $this->elementName=$elementName;
    }
    private function addWinning(Element $element):void{
        $this->winsOver[] = $element;
    }
    public function addWinnings(array $elements):void{
        foreach ($elements as $element){
            if(!$element instanceof Element) continue;
            $this->addWinning($element);
        }
    }
    public function getElementName():string{
        return $this->elementName;
    }
    public function winsAgainst(Element $element):bool{
        return in_array($element, $this->winsOver);
    }
}
class Player{
    private string $playerName;
    private Element $hand;
    private bool $isCPU;
    public function __construct(string $playerName, bool $isCPU)
    {
        $this->playerName=$playerName;
        $this->isCPU=$isCPU;
    }
    public function setHand(Element $element): void{
        $this->hand=$element;
    }
    public function getPlayerName(): string
    {
        return $this->playerName;
    }
    public function getHand(): Element
    {
        return $this->hand;
    }
    public function isCPU(): bool
    {
        return $this->isCPU;
    }

}
class Game{
    /** @var Element[] */
    private array $elements =[];
    private ?Player $winner = null;
    private Player $attacker;
    private Player $defender;
    public function __construct(Player $attacker, Player $defender)
    {
        $this->attacker=$attacker;
        $this->defender=$defender;
        $this->setup();
    }
    public function setup():void{
        $this->elements=[
            $rock = new Element('Rock'),
            $paper = new Element('Paper'),
            $scissors = new Element('Scissors'),
        ];
        $rock->addWinnings([$scissors]);
        $paper->addWinnings([$rock]);
        $scissors->addWinnings([$paper]);
    }
    public function getElements():array{
        return $this->elements;
    }
    public function determineResult():void
    {
        if($this->attacker->getHand() === $this->defender->getHand()){
            $this->winner=null;
        }
        if($this->attacker->getHand()->winsAgainst($this->defender->getHand())){
            $this->winner=$this->defender;
         //   var_dump($this->attacker->getHand()->getElementName(), $this->defender->getHand()->getElementName());
        }
        if($this->defender->getHand()->winsAgainst($this->attacker->getHand())){
            $this->winner=$this->attacker;
        }
    }
    public function isTied():bool
    {
        return is_null($this->winner);
    }
    public function getWinner(): ?Player
    {
        return $this->winner;
    }
}
class GameSet{
    private Player $attacker;
    private Player $defender;
    private Player $winner;
    private array $games=[];
    private const MAX_WINS=2;
    private int $attackerPoints=0;
    private int $defenderPoint=0;
    public function __construct(Player $attacker, Player $defender)
    {
        $this->attacker=$attacker;
        $this->defender=$defender;
    }

    public function determineResult(): void
    {
        while($this->attackerPoints < self::MAX_WINS && $this->defenderPoint < self::MAX_WINS){
            $game = new Game($this->attacker, $this->defender);
            $this->games[] = $game;
            $elements = $game->getElements();
            $attackerSelectedIndex = array_rand($elements);
            $defenderSelectedIndex = array_rand($elements);
            $this->attacker->setHand($elements[$attackerSelectedIndex]);
            $this->defender->setHand($elements[$defenderSelectedIndex]);
            $game->determineResult();
            //var_dump($this->attackerPoints, $this->defenderPoint);
            if($game->isTied()){
                continue;
            }
            if($game->getWinner()===$this->attacker){
                $this->attackerPoints++;
            }
            if($game->getWinner()===$this->defender){
                $this->defenderPoint++;
            }
        }
        if($this->attackerPoints > $this->defenderPoint){
            $this->winner=$this->attacker;
            return;
        }

        $this->winner=$this->defender;

    }
    public function getAttacker(): Player
    {
        return $this->attacker;
    }
    public function getDefender(): Player
    {
        return $this->defender;
    }
    public function getWinner(): Player
    {
        return $this->winner;
    }
    public function getAttackerPoints(): int
    {
        return $this->attackerPoints;
    }
    public function getDefenderPoint(): int
    {
        return $this->defenderPoint;
    }
    public function getGames(): array
    {
        return $this->games;
    }
}
class Tournament{
    ///Functions like => determineResult
    ///Function => setPlayers, getPlayers
    ///Function => setPlaces, getPlaces (array_unshift)
    private array $players;
    private array $winner = [];
    public function addPlayer(Player $player): void{
        $this->players[] = $player;
    }
    public function addPlayers(array $players): void{
        foreach ($players as $player){
            if(!$player instanceof Player) continue;
            $this->addPlayer($player);
        }
    }
    public function determineResult(Player $p1, Player $p2): void{
        $set = new GameSet($p1, $p2);
        $set->determineResult();
        array_unshift($this->winner, $set->getWinner());
    }
    public function getPlayers(): array
    {
        return $this->players;
    }
    public function getWinner(): array
    {
        return $this->winner;
    }
}

$t = new Tournament();
$t->addPlayers([new Player('CPU1', true), new Player('CPU2', true), new Player('CPU3', true), new Player('CPU4', true),new Player('CPU5', true),new Player('CPU6', true),new Player('CPU7', true),new Player('CPU8', true),]);
$t->determineResult($t->getPlayers()[0], $t->getPlayers()[1]);
$t->determineResult($t->getPlayers()[2], $t->getPlayers()[3]);
$t->determineResult($t->getPlayers()[4], $t->getPlayers()[5]);
$t->determineResult($t->getPlayers()[6], $t->getPlayers()[7]);
$winner = $t->getWinner();
$t->determineResult($winner[3], $winner[2]);
$t->determineResult($winner[2], $winner[1]);
$t->determineResult($winner[1], $winner[0]);


foreach ($t->getWinner() as $key => $player) {
    echo $key == 0? "[$key] {$player->getPlayerName()}".PHP_EOL:"[$key] {$player->getPlayerName()}".PHP_EOL;
}
//$gameSet1 = new GameSet(new Player('CPU1'), new Player('CPU2'));
//$gameSet1->determineResult();
//$gameSet2 = new GameSet(new Player('CPU3'), new Player('CPU4'));
//$gameSet2->determineResult();
//$gameSet3 = new GameSet(new Player('CPU5'), new Player('CPU6'));
//$gameSet3->determineResult();
//$gameSet4 = new GameSet(new Player('CPU7'), new Player('CPU8'));
//$gameSet4->determineResult();
//$gameSet5 = new GameSet($gameSet1->getWinner(), $gameSet2->getWinner());
//$gameSet5->determineResult();
//$gameSet6 = new GameSet($gameSet3->getWinner(), $gameSet4->getWinner());
//$gameSet6->determineResult();
//$gameSet7 = new GameSet($gameSet5->getWinner(), $gameSet6->getWinner());
//$gameSet7->determineResult();
//echo "---------- quater -------------".PHP_EOL;
//echo "{$gameSet1->getAttacker()->getPlayerName()}({$gameSet1->getAttackerPoints()}) vs {$gameSet1->getDefender()->getPlayerName()}({$gameSet1->getDefenderPoint()}) | Winner {$gameSet1->getWinner()->getPlayerName()}".PHP_EOL;
//printGames($gameSet1);
//echo "Winner is {$gameSet2->getAttacker()->getPlayerName()}({$gameSet2->getAttackerPoints()}) vs {$gameSet2->getDefender()->getPlayerName()}({$gameSet2->getDefenderPoint()}) | Winner {$gameSet2->getWinner()->getPlayerName()}".PHP_EOL;
//echo "Winner is {$gameSet3->getAttacker()->getPlayerName()}({$gameSet3->getAttackerPoints()}) vs {$gameSet3->getDefender()->getPlayerName()}({$gameSet3->getDefenderPoint()}) | Winner {$gameSet3->getWinner()->getPlayerName()}".PHP_EOL;
//echo "Winner is {$gameSet4->getAttacker()->getPlayerName()}({$gameSet4->getAttackerPoints()}) vs {$gameSet4->getDefender()->getPlayerName()}({$gameSet4->getDefenderPoint()}) | Winner {$gameSet4->getWinner()->getPlayerName()}".PHP_EOL;
//echo "---------- semi -------------".PHP_EOL;
//echo "Winner is {$gameSet5->getAttacker()->getPlayerName()}({$gameSet5->getAttackerPoints()}) vs {$gameSet5->getDefender()->getPlayerName()}({$gameSet5->getDefenderPoint()}) | Winner {$gameSet5->getWinner()->getPlayerName()}".PHP_EOL;
//echo "Winner is {$gameSet6->getAttacker()->getPlayerName()}({$gameSet6->getAttackerPoints()}) vs {$gameSet6->getDefender()->getPlayerName()}({$gameSet6->getDefenderPoint()}) | Winner {$gameSet6->getWinner()->getPlayerName()}".PHP_EOL;
//
//echo "---------- final -------------".PHP_EOL;
//echo "Winner is {$gameSet7->getAttacker()->getPlayerName()}({$gameSet7->getAttackerPoints()}) vs {$gameSet7->getDefender()->getPlayerName()}({$gameSet7->getDefenderPoint()}) | Winner {$gameSet7->getWinner()->getPlayerName()}".PHP_EOL;
//
//printGames($gameSet7);
//
//function printGames(GameSet $gameSetGames):void{
//    foreach ($gameSetGames->getGames() as $index => $game) {
//        $number = $index+1;
//        echo $game->isTied()? "[$number] It's a tie".PHP_EOL:"[$number] {$game->getWinner()->getPlayerName()}".PHP_EOL;
//    }
//}