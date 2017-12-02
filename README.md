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

```composer global require rucd/webshell-detector```

This will install webshell-detector to your global vendor binaries directory
(usually **~/.config/composer/vendor/bin**). If this directory is part of your path,
you can run it directly:

```webshell-detector <filename>```

Or, if the global vendor binaries directory is not part of your path:

```~/.config/composer/vendor/bin/webshell-detector <filename>```




