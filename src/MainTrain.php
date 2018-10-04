<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require __DIR__ . "/../vendor/autoload.php";

use RUCD\WebshellDetector\TrainDetector;
use RUCD\Training\Trainer;
use RUCD\Training\TrainerParameters;
use Aggregation\WOWA;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;



error_reporting(E_ERROR | E_NOTICE | E_PARSE);
echo "Beginning of webshell analysis \n";
$dataFile = tempnam(__DIR__ . "/../trainer_files", 'data_file_');
$expectedFile = tempnam(__DIR__ . "/../trainer_files", 'expected_file_');
//$weights = tempnam(__DIR__ . "/../trainer_files", 'weights_');

$detector = new TrainDetector();
echo __DIR__;
echo "\n";
$data = iterator_to_array(
    $detector->analyzeDirectory(
        __DIR__ . "/..", 
        $dataFile, 
        $expectedFile 
    )
);

//var_dump(unserialize(file_get_contents($dataFile)));
//var_dump(unserialize(file_get_contents($expectedFile)));

echo "PHP files analyzed !\n";
$fileNumber = count(unserialize(file_get_contents($dataFile)));
echo "There are : $fileNumber analyzed!\n";

$logger = new Logger('wowa-training-test');
$logger->pushHandler(new StreamHandler('php://stdout', Logger::DEBUG));


$populationSize = $argv[1];
$crossoverRate = $argv[2];
$mutationRate = $argv[3];
$selectionMethod = TrainerParameters::SELECTION_METHOD_RWS;
$maxGenerationNumber = $argv[4];
$generationInitialPopulationMethod = $argv[5];

if ($generationInitialPopulationMethod == 'RANDOM') {
    $generationInitialPopulationMethod = TrainerParameters::INITIAL_POPULATION_GENERATION_RANDOM;
} elseif ($generationInitialPopulationMethod == 'QUASI_RANDOM') {
    $generationInitialPopulationMethod = TrainerParameters::INITIAL_POPULATION_GENERATION_QUASI_RANDOM;
}

$parameters = new TrainerParameters(
    $logger, 
    $populationSize, 
    $crossoverRate, 
    $mutationRate, 
    $selectionMethod, 
    $maxGenerationNumber,
    $generationInitialPopulationMethod
);
$trainer = new Trainer($parameters);
$result = $trainer->run(
    unserialize(file_get_contents($dataFile)), 
    unserialize(file_get_contents($expectedFile))
);
file_put_contents($weights, serialize($result));
var_dump($result);

file_put_contents('Comparison.txt', "Expected       WOWA        Average\n");
for ($i = 0; $i < count($data); $i++) {
    $wowa = WOWA::wowa($result->weights_w, $result->weights_p, unserialize(file_get_contents($dataFile))[$i]);
    $average = array_sum(unserialize(file_get_contents($dataFile))[$i]) / count(unserialize(file_get_contents($dataFile))[$i]);
    $expected = unserialize(file_get_contents($expectedFile))[$i];
    file_put_contents(
        'Comparison.txt',
              "$expected    $wowa   $average\n",
        FILE_APPEND
    );
    if ($expected[$i] === 1) {
        echo "$expected[$i] : $wowa : $average\n";
    }  
}