<?php
require_once 'src/FormBuilder.php';

$formBuilder = new FormBuilder;
$formBuilder->parseJson('_data/form.json');
$formBuilder->build();
$inputs = $formBuilder->getInputs();

foreach ($inputs as $input) {
    echo $input;
}