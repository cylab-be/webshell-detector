<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once "vendor/autoload.php";

use RUCD\WebshellDetector\TrainDetector;
use RUCD\Training\Trainer;
use RUCD\Training\TrainerParameters;
use Aggregation\WOWA;


//error_reporting(E_ERROR);

$dataFile = tempnam(__DIR__ . "/../trainer_file", 'data_file_');
$expectedFile = tempnam(__DIR__ . "/../trainer_file", 'expected_file');
$weights = tempnam(__DIR__ . "/../trainer_file", 'weights_');

$detector = new TrainDetector();
echo __DIR__;
echo "\n";
$data = iterator_to_array(
    $detector->analyzeDirectory(
        __DIR__ . "/../tests", 
        $dataFile, 
        $expectedFile 
    )
);
//$data = $detector->analyzeDirectory("/home/alex/Projects/webshell-detector/tests/res/webshells_modified");
foreach ($data as $key => $score) {
    echo "$score : $key \n";
    //var_dump($score);
}


var_dump(unserialize(file_get_contents($dataFile)));
var_dump(unserialize(file_get_contents($expectedFile)));

$populationSize = 120;
$crossoverRate = 50;
$mutationRate = 19;
$selectionMethod = TrainerParameters::SELECTION_METHOD_RWS;
$maxGenerationNumber = 120;
$parameters = new TrainerParameters(
    null, 
    $populationSize, 
    $crossoverRate, 
    $mutationRate, 
    $selectionMethod, 
    $maxGenerationNumber
);
$trainer = new Trainer($parameters);
$result = $trainer->run(
    unserialize(file_get_contents($dataFile)), 
    unserialize(file_get_contents($expectedFile))
);
file_put_contents($weights, serialize($result));
var_dump($result);

foreach (unserialize(file_get_contents($dataFile)) as $line) {
    echo WOWA::wowa($result->weights_w, $result->weights_p, $line);
    echo "\n";
}