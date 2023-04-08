:: Run easy-coding-standard (ecs) via this batch file inside your IDE e.g. PhpStorm (Windows only)
:: Install inside PhpStorm the  "Batch Script Support" plugin
cd..
cd..
cd..
cd..
cd..
cd..
php vendor\bin\ecs check vendor/markocupic/contao-php2xliff/src --fix --config vendor/markocupic/contao-php2xliff/tools/ecs/config.php
php vendor\bin\ecs check vendor/markocupic/contao-php2xliff/contao --fix --config vendor/markocupic/contao-php2xliff/tools/ecs/config.php
php vendor\bin\ecs check vendor/markocupic/contao-php2xliff/config --fix --config vendor/markocupic/contao-php2xliff/tools/ecs/config.php
:: php vendor\bin\ecs check vendor/markocupic/contao-php2xliff/templates --fix --config vendor/markocupic/contao-php2xliff/tools/ecs/config.php
::php vendor\bin\ecs check vendor/markocupic/contao-php2xliff/tests --fix --config vendor/markocupic/contao-php2xliff/tools/ecs/config.php


