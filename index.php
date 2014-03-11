<?php
require_once 'src/FormBuilder.php';

$formBuilder = new FormBuilder;
$formBuilder->parseJson(file_get_contents('_data/form.json'));

echo "<pre>";
die(var_dump($formBuilder->getBlueprint()));

$formBuilder->build();
$elements = $formBuilder->getElements();

foreach ($elements as $element) {
    echo $element, "<br>";
}