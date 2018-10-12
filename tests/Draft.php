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

use Kachkaev\PHPR\RCore;
use Kachkaev\PHPR\Engine\CommandLineREngine;

$data = unserialize(file_get_contents(__DIR__ . "/../trainer_files/data_file_s3HOfm"));
$expected = unserialize(file_get_contents(__DIR__ . "/../trainer_files/expected_file_jG9C2M"));
$result = new \RUCD\Training\Solution();
//$result->weights_w = array(0.0015686208709128, 0.81609962234305, 0.1566542800825, 0.0080763702019774, 0.017601106501563);
//$result->weights_p = array(0.20521495661322, 0.54284862595585, 0, 0.25020780186282, 0.0017286155681124);

//$result->weights_w = array(0.077559349785065, 0.8779099179324, 0.040866865415455, 0.0015647334419612, 0.0020991334251164);
//$result->weights_p = array(0.28874073390941, 0.44975766454604, 0.0078149707012648, 0.23995395668512, 0.013732674158171);

$result->weights_w = array(1,0,0,0,0);
$result->weights_p = array(0.1, 0.3, 0.3, 0.2, 0.1);

$rocValuesWowa = array();
$rocValuesAverage = array();

for ($i = 0; $i < count($data); $i++) {
    $wowa = WOWA::wowa(
        $result->weights_w, 
        $result->weights_p, 
        $data[$i]
    );
    $average = array_sum($data[$i]) / count($data[$i]);
    array_push($rocValuesWowa, array($expected[$i], $wowa, $average));
}

$fpWowa = fopen('RocWowa.csv', 'w');
fputcsv($fpWowa, array('Expected', 'Wowa', 'Average'));
foreach ($rocValuesWowa as $element) {
    fputcsv($fpWowa, $element);
}
  
