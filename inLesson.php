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
    public function getIsCPU():bool{
        return $this->isCPU;
    }
}
class GamePlay{
    private array $elements =[];
    public function __construct()
    {
        $this->setup();
    }
    public function start(Player $p1, Player $p2, int $p1Index, int $p2Index):?Player{
        $p11 =$p1;
        $p21 =$p2;
        $p11->setHand($this->elements[$p1Index]);
        $p21->setHand($this->elements[$p2Index]);
        echo $p11->getPlayerName()." uses ".$p11->getHand()->getElementName()." against ". $p21->getPlayerName()."'s "
            .$p21->getHand()->getElementName().PHP_EOL;
        if($p11->getHand() === $p21->getHand()){
            return null;
        }
        if($p21->getHand()->winsAgainst($p11->getHand())){
            return $p21;
        }
        if($p11->getHand()->winsAgainst($p21->getHand())){
            return $p11;
        }
        return null;
    }
    public function setup():void{
        $this->elements=[
            $rock = new Element('Rock'),
            $paper = new Element('Paper'),
            $scissors = new Element('Scissors'),
            $lizard = new Element('Lizard'),
            $spock = new Element('Spock'),
        ];
        $rock->addWinnings([$scissors, $lizard]);
        $paper->addWinnings([$rock, $spock]);
        $scissors->addWinnings([$paper, $lizard]);
        $lizard->addWinnings([$paper, $spock]);
        $spock->addWinnings([$scissors, $rock]);

    }
    public function displayElements():void{
        foreach ($this->elements as $keys => $element) {
            echo "[{$keys}] - {$element->getElementName()}".PHP_EOL;
        }
    }
    public function getElements():array{
        return $this->elements;
    }

}
class GameTournament{
    private array $score=[];
    private array $players=[];
    private GamePlay $game;
    public function __construct()
    {
        $this->setup();
        $this->game = new GamePlay();
    }

    public function startTournament():void
    {
        $winnerFound = false;
        while(!$winnerFound){
            echo count($this->players).PHP_EOL;
            $p1Index=$this->getIndex();
            $p2Index=$this->getIndex();
            if($p1Index != $p2Index){
                $p1Score =0;
                $p2Score =0;
                $bo3=false;
                echo  '---------------------------'.PHP_EOL;
                if($this->players[$p1Index]->getIsCPU()&&$this->players[$p2Index]->getIsCPU()){
                    while(!$bo3){
                        $p1MoveIndex = rand(0, count($this->game->getElements())-1);
                        $p2MoveIndex = rand(0, count($this->game->getElements())-1);
                        $winner = $this->game->start($this->players[$p1Index], $this->players[$p2Index], $p1MoveIndex, $p2MoveIndex);
                        if($winner === $this->players[$p1Index]){
                            $p1Score++;
                        }elseif($winner=== $this->players[$p2Index]){
                            $p2Score++;
                        }
                        echo $winner != null ? $winner->getPlayerName()." has won".PHP_EOL:"It's a tie".PHP_EOL;

                        if($p1Score >= 2 || $p2Score >= 2){
                            $bo3 = true;
                        }
                    }
                }else{
                    while(!$bo3) {
                        $this->game->displayElements();
                        $playerMoveIndex = (int)readLine('Choose type: ');
                        $playerIndex = $this->players[$p1Index]->getIsCPU() ? $this->players[$p2Index] : $this->players[$p1Index];
                        $cpu = $this->players[$p1Index]->getIsCPU() ? $this->players[$p1Index] : $this->players[$p2Index];
                        $cpuMoveIndex = rand(0, count($this->game->getElements()) - 1);
                        $winner = $this->game->start($cpu, $playerIndex, $cpuMoveIndex, $playerMoveIndex);
                        if ($winner === $cpu) {
                            $p1Score++;
                        } elseif ($winner === $playerIndex) {
                            $p2Score++;
                        }
                        echo $winner != null ? $winner->getPlayerName()." has won" . PHP_EOL : "It's a tie" . PHP_EOL;

                        if ($p1Score >= 2 || $p2Score >= 2) {
                            $bo3 = true;
                        }
                    }
                }
                if($p1Score >= 2){
                    $this->score[] = $this->players[$p2Index];
                    echo $this->players[$p2Index]->getPlayerName().' has been eliminated'.PHP_EOL;
                    array_splice($this->players, $p2Index, 1);
                }
                if($p2Score >= 2){
                    $this->score[] = $this->players[$p1Index];
                    echo $this->players[$p1Index]->getPlayerName().' has been eliminated'.PHP_EOL;
                    array_splice($this->players, $p1Index, 1);
                }
                echo  '---------------------------'.PHP_EOL;
            }else{
                continue;
            }

            if(count($this->players) === 1){
                $this->score[] = $this->players[0];
                $winnerFound=true;
            }
        }
        $reverseScore = array_reverse($this->score);
        $key= 1;
        foreach($reverseScore as $sco){
            echo "|". $key ." place - ".$sco->getPlayerName();
            $key++;
        }
    }
    public function getIndex():int{
        return rand(0, count($this->players)-1);
    }
    private function setup():void
    {
        for($i = 0; $i <= 8; $i++){
            if($i==0){
                $this->players[]= new Player('Player', false);

            }
            $this->players[] = new Player("CPU$i", true);
        }

    }

}

$gm = new GameTournament();
$gm->startTournament();
