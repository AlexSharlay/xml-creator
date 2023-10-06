<?php

require_once 'FileGenerator.php';
require_once 'XmlConverter.php';

ini_set('max_execution_time', '600');

//$fileGenerator = new FileGenerator('InputFiles');
//$fileGenerator->generateTxt('file1.txt');

$xmlConverter = new XmlConverter('InputFiles\file1.txt', 'OutputFiles\file1.xml');
$xmlConverter->convert();
