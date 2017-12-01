# webshell-detector

[![Build Status](https://travis-ci.org/RUCD/webshell-detector.svg?branch=master)](https://travis-ci.org/RUCD/webshell-detector) [![Latest Stable Version](https://poser.pugx.org/rucd/webshell-detector/v/stable)](https://packagist.org/packages/rucd/webshell-detector) [![Total Downloads](https://poser.pugx.org/rucd/webshell-detector/downloads)](https://packagist.org/packages/rucd/webshell-detector) [![Latest Unstable Version](https://poser.pugx.org/rucd/webshell-detector/v/unstable)](https://packagist.org/packages/rucd/webshell-detector) [![License](https://poser.pugx.org/rucd/webshell-detector/license)](https://packagist.org/packages/rucd/webshell-detector)

## Installation

```composer require rucd/webshell-detector```

## Usage

```php
require_once "vendor/autoload.php";

use RUCD\WebshellDetector\Detector;

$detector = new Detector();
echo $detector->analyzeFile("strange_file.php");
```