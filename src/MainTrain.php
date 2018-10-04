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
$weightFile = tempnam(__DIR__ . "/../trainer_files", 'weights_');

$detector = new TrainDetector();
echo __DIR__;
echo "\n";
$analyzedData = iterator_to_array(
    $detector->analyzeDirectory(
        __DIR__ . "/..", 
        $dataFile, 
        $expectedFile 
    )
);

$data = unserialize(file_get_contents($dataFile));
$expected = unserialize(file_get_contents($expectedFile));

//var_dump(unserialize(file_get_contents($dataFile)));
//var_dump(unserialize(file_get_contents($expectedFile)));

echo count($expected) . " files analyzed\n";
echo array_sum($expected) . " files malicious\n";
echo "Begining of wowa-training\n";

$logger = new Logger('wowa-training-test');
$logger->pushHandler(new StreamHandler('php://stdout', Logger::DEBUG));


$populationSize = $argv[1];
$crossoverRate = $argv[2];
$mutationRate = $argv[3];
$selectionMethod = TrainerParameters::SELECTION_METHOD_RWS;
$maxGenerationNumber = $argv[4];
$generationInitialPopulationMethod = $argv[5];

if ($generationInitialPopulationMethod == 'RANDOM') {
    $generationInitialPopulationMethod 
        = TrainerParameters::INITIAL_POPULATION_GENERATION_RANDOM;
} elseif ($generationInitialPopulationMethod == 'QUASI_RANDOM') {
    $generationInitialPopulationMethod 
        = TrainerParameters::INITIAL_POPULATION_GENERATION_QUASI_RANDOM;
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
$result = $trainer->run($data, $expected);
file_put_contents($weightFile, serialize($result));
var_dump($result);

echo "Beginning of statistical analysis\n";

$wowaTruePositiveCounter = 0;
$wowaTrueNegativeCounter = 0;
$averageTruePositiveCounter = 0;
$averageTrueNegativeCounter = 0;

$wowaFalsePositiveCounter = 0;
$wowaFalseNegativeCounter = 0;
$averageFalsePositiveCounter = 0;
$averageFalseNegativeCounter = 0;


for ($triggerValue = 0.05; $triggerValue < 0.95; $triggerValue+=0.05) {
    for ($i = 0; $i < count($data); $i++) {
        $wowa = WOWA::wowa(
            $result->weights_w, 
            $result->weights_p, 
            $data[$i]
        );
        $average = array_sum($data[$i]) / count($data[$i]);
        if ($expected[$i] === 1 && $average > $triggerValue && $wowa > $triggerValue) {
            $averageTruePositiveCounter++;
            $wowaTruePositiveCounter++;
        }
        elseif ($expected[$i] === 1 && $average < $triggerValue && $wowa > $triggerValue) {
            $wowaTruePositiveCounter++;
            $averageFalseNegativeCounter++;
        }
        elseif ($expected[$i] === 1 && $average > $triggerValue && $wowa < $triggerValue) {
            $averageTruePositiveCounter++;
            $wowaFalseNegativeCounter++;
        }
        elseif ($expected[$i] === 1 && $average < $triggerValue && $wowa < $triggerValue) {
            $averageFalseNegativeCounter++;
            $wowaFalseNegativeCounter++;
        }
        elseif ($expected[$i] === 0 && $average < $triggerValue && $wowa < $triggerValue) {
            $averageTrueNegativeCounter++;
            $wowaTrueNegativeCounter++;
        }
        elseif ($expected[$i] === 0 && $average < $triggerValue && $wowa > $triggerValue) {
            $averageTrueNegativeCounter++;
            $wowaFalsePositiveCounter++;
        }
        elseif ($expected[$i] === 0 && $average > $triggerValue && $wowa < $triggerValue) {
            $wowaTrueNegativeCounter++;
            $averageFalsePositiveCounter++;
        }
        elseif ($expected[$i] === 0 && $average > $triggerValue && $wowa > $triggerValue) {
            $averageFalsePositiveCounter++;
            $wowaFalsePositiveCounter++;
        }
    }
    //Positive conditions
    $wowaPositiveCondition = $wowaTruePositiveCounter + $wowaFalseNegativeCounter;
    $averagePositiveCondition = $averageTruePositiveCounter + $averageFalseNegativeCounter;
    
    //Negative condition
    $wowaNegativeCondition = $wowaTrueNegativeCounter + $wowaFalsePositiveCounter;
    $averageNegativeCondition = $averageTrueNegativeCounter + $averageFalsePositiveCounter;
    
    //True positive rate
    $wowaTruePositiveRate = $wowaTruePositiveCounter / $wowaPositiveCondition;
    $averageTruePositiveRate = $averageTruePositiveCounter / $averagePositiveCondition;
    
    //True negative Rate
    $wowaTrueNegativeRate = $wowaTrueNegativeCounter / $wowaNegativeCondition;
    $averageTrueNegativeRate = $averageTrueNegativeCounter /$averageNegativeCondition;
    
    //False negative rate
    $wowaFalseNegativeRate = 1 - $wowaTruePositiveRate;
    $averageFalseNegativeRate = 1 - $averageTruePositiveRate;
    
    //False positive rate
    $wowaFalsePositiveRate = 1 - $wowaTrueNegativeRate;
    $averageFalsePositiveRate = 1 - $averageTrueNegativeRate;
    
    //Precision (or positive predictive value)
    $wowaPrecision = $wowaTruePositiveCounter / ($wowaTruePositiveCounter + $wowaFalsePositiveCounter);
    $averagePrecision = $averageTruePositiveCounter / ($averageTruePositiveCounter + $averageFalsePositiveCounter);
    
    //Negative predictive value
    $wowaNegativePredictiveValue = $wowaTrueNegativeCounter / ($wowaTrueNegativeCounter + $wowaFalseNegativeCounter);
    $averageNegativePredictiveValue = $averageTrueNegativeCounter / ($averageTrueNegativeCounter + $averageFalseNegativeCounter);
    
    //False discovery rate
    $wowaFalseDiscoveryRate = 1 - $wowaPrecision;
    $averageFalseDiscoveryRate = 1 - $averagePrecision;
    
    //False omission rate
    $wowaFalseOmissionRate = 1 - $wowaNegativePredictiveValue;
    $averageFalseOmissionRate = 1 - $averageNegativePredictiveValue;
    
    //Accuracy
    $wowaAccuracy = ($wowaTruePositiveCounter + $wowaTrueNegativeCounter) / count($expected);
    $averageAccuracy = ($averageTruePositiveCounter + $averageTrueNegativeCounter) /count($expected);
    
    //F1 score
    $wowaF1 = 2 * ($wowaPrecision * $wowaTruePositiveRate) / ($wowaPrecision + $wowaTruePositiveRate);
    $averageF1 = 2 * ($averagePrecision * $averageTruePositiveRate) / ($averagePrecision + $averageTruePositiveRate);
    
    
    file_put_contents('Statistical_results.txt', "_____________________________\n", FILE_APPEND);
    file_put_contents('Statistical_results.txt', "|TRIGGER VALUE        : $triggerValue |\n", FILE_APPEND);
    file_put_contents('Statistical_results.txt', "|____________________________|\n\n", FILE_APPEND);
    file_put_contents('Statistical_results.txt', "WOWA\n", FILE_APPEND);
    file_put_contents('Statistical_results.txt', "Wowa true positive : $wowaTruePositiveCounter \n", FILE_APPEND);
    file_put_contents('Statistical_results.txt', "Wowa true negative : $wowaTrueNegativeCounter \n", FILE_APPEND);
    file_put_contents('Statistical_results.txt', "Wowa false positive : $wowaFalsePositiveCounter\n", FILE_APPEND);
    file_put_contents('Statistical_results.txt', "Wowa false negative : $wowaFalseNegativeCounter\n", FILE_APPEND);
    file_put_contents('Statistical_results.txt', "Wowa true positive rate : $wowaTruePositiveRate\n", FILE_APPEND);
    file_put_contents('Statistical_results.txt', "Wowa true negative rate : $wowaTrueNegativeRate\n", FILE_APPEND);
    file_put_contents('Statistical_results.txt', "Wowa false positive rate : $wowaFalsePositiveRate\n", FILE_APPEND);
    file_put_contents('Statistical_results.txt', "Wowa false negative rate : $wowaFalseNegativeRate\n", FILE_APPEND);
    file_put_contents('Statistical_results.txt', "Wowa precision : $wowaPrecision\n", FILE_APPEND);
    file_put_contents('Statistical_results.txt', "Wowa negative predictive value : $wowaNegativePredictiveValue\n", FILE_APPEND);
    file_put_contents('Statistical_results.txt', "Wowa false discovery rate : $wowaFalseDiscoveryRate\n", FILE_APPEND);
    file_put_contents('Statistical_results.txt', "Wowa false omission rate : $wowaFalseOmissionRate\n", FILE_APPEND);
    file_put_contents('Statistical_results.txt', "Wowa accuracy : $wowaAccuracy\n", FILE_APPEND);
    file_put_contents('Statistical_results.txt', "Wowa F1 score : $wowaF1\n\n", FILE_APPEND);
    
    file_put_contents('Statistical_results.txt', "AVERAGE\n", FILE_APPEND);
    file_put_contents('Statistical_results.txt', "Average true positive : $averageTruePositiveCounter \n", FILE_APPEND);
    file_put_contents('Statistical_results.txt', "Average true negative : $averageTrueNegativeCounter \n", FILE_APPEND);
    file_put_contents('Statistical_results.txt', "Average false positive : $averageFalsePositiveCounter\n", FILE_APPEND);
    file_put_contents('Statistical_results.txt', "Average false negative : $averageFalseNegativeCounter\n", FILE_APPEND);
    file_put_contents('Statistical_results.txt', "Average true positive rate : $averageTruePositiveRate\n", FILE_APPEND);
    file_put_contents('Statistical_results.txt', "Average true negative rate : $averageTrueNegativeRate\n", FILE_APPEND);
    file_put_contents('Statistical_results.txt', "Average false positive rate : $averageFalsePositiveRate\n", FILE_APPEND);
    file_put_contents('Statistical_results.txt', "Average false negative rate : $averageFalseNegativeRate\n", FILE_APPEND);
    file_put_contents('Statistical_results.txt', "Average precision : $averagePrecision\n", FILE_APPEND);
    file_put_contents('Statistical_results.txt', "Average negative predictive value : $averageNegativePredictiveValue\n", FILE_APPEND);
    file_put_contents('Statistical_results.txt', "Average false discovery rate : $averageFalseDiscoveryRate\n", FILE_APPEND);
    file_put_contents('Statistical_results.txt', "Average false omission rate : $averageFalseOmissionRate\n", FILE_APPEND);
    file_put_contents('Statistical_results.txt', "Average accuracy : $averageAccuracy\n", FILE_APPEND);
    file_put_contents('Statistical_results.txt', "Average F1 score : $averageF1\n\n", FILE_APPEND);
    
    $wowaTrueNegativeCounter = 0;
    $averageTruePositiveCounter = 0;
    $averageTrueNegativeCounter = 0;

    $wowaFalsePositiveCounter = 0;
    $wowaFalseNegativeCounter = 0;
    $averageFalsePositiveCounter = 0;
    $averageFalseNegativeCounter = 0;
    
}