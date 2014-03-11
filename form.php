<?php

class FormBuilder {

    protected $tags = array(
        'labels' => '<label {{data}}>{{value}}</label>',
        'input' => '<input {{data}}>',
        'textarea' => '<textarea {{data}}>{{value}}</textarea>'
    );

    protected $elements = array();

    public function __construct($json)
    {
        $blueprint = json_decode($json);

        if (is_null($blueprint))
            throw new InvalidArgumentException('Invalid JSON supplied to ['.__CLASS__.']');

        foreach ($blueprint->data as $elementName => $elementData) {

            if (! array_key_exists($elementName, $this->tags))
                continue; // Don't know how to build it

            $elements[] = $this->constructElement($elementName, (array)$elementData);

        }

        $this->elements = $elements;
    }

    public function getElements()
    {
        return $this->elements;
    }

    protected function constructElement($element, array $data)
    {
        $tag = $this->tags[$element];

        if (strstr($tag, '{{value}}')) {
            $tag = preg_replace('/{{value}}/', $data['value'], $tag);
            unset($data['value']);
        }

        return preg_replace_callback('/{{data}}/', function () use ($data) {
            return $this->buildInnards($data);
        }, $tag);
    }

    protected function buildInnards(array $data)
    {
        $str = '';
        foreach ($data as $key => $value) {
            $str .= $key . '="' . $value . '" ';
        }
        $str = trim($str);
        return $str;
    }
}

try {
    $formBuilder = new FormBuilder(file_get_contents('form.json'));
    foreach ($formBuilder->getElements() as $element) {
        echo $element;
    }
} catch (InvalidArgumentException $e) {
    echo $e->getMessage();
}