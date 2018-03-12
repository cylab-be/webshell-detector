# webshell-detector

[![Build Status](https://travis-ci.org/RUCD/webshell-detector.svg?branch=master)](https://travis-ci.org/RUCD/webshell-detector) [![Latest Stable Version](https://poser.pugx.org/rucd/webshell-detector/v/stable)](https://packagist.org/packages/rucd/webshell-detector) [![Total Downloads](https://poser.pugx.org/rucd/webshell-detector/downloads)](https://packagist.org/packages/rucd/webshell-detector) [![Latest Unstable Version](https://poser.pugx.org/rucd/webshell-detector/v/unstable)](https://packagist.org/packages/rucd/webshell-detector) [![License](https://poser.pugx.org/rucd/webshell-detector/license)](https://packagist.org/packages/rucd/webshell-detector)

## Installation and usage

The webshell detector can be integrated as a composer library to your project,
or you can run it from the command line.

### As a library

```composer require rucd/webshell-detector```

```php
require_once "vendor/autoload.php";

use RUCD\WebshellDetector\Detector;

$detector = new Detector();
echo $detector->analyzeFile("strange_file.php");
```

### From the command line

Download the runnable PHAR from the [Releases pages](https://github.com/RUCD/webshell-detector/releases).

To run:

```
webshell-detector.phar analyze:directory /path/to/directory
```

You can modify the "sensitivity" of the detector, by modifying the threshold for displaying files. This will display the suspiciousness score of every files:

```
webshell-detector.phar analyze:directory -t 0.0 /path/to/directory
```

The default threshold used by the tool is 0.4

