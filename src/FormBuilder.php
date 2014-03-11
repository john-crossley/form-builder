<?php

class FormBuilder {

    protected $_blueprint = null;
    protected $_elements = array();
    protected $_tags = array(
        'label' => '<label {{data}}>{{value}}</label>',
        'input' => '<input {{data}}>',
        'textarea' => '<textarea {{data}}>{{value}}</textarea>'
    );

    public function parseJson($json)
    {
        $this->_blueprint = json_decode($json);
        if (is_null($this->_blueprint))
            throw new InvalidArgumentException('You supplied invalid JSON to ['.__CLASS__.']');
        return true;
    }

    public function getBlueprint()
    {
        return $this->_blueprint;
    }

    public function getElements()
    {
        return $this->_elements;
    }

    public function addTag($tagName, $tagBlueprint)
    {
        $this->_tags[$tagName] = $tagBlueprint;
    }

    public function getTags()
    {
        return $this->_tags;
    }

    public function build()
    {
        foreach ($this->_blueprint->data as $elementObjectData) {
            foreach ($elementObjectData as $elementName => $elementData) {
                if (! array_key_exists($elementName, $this->_tags)) {
                    throw new UnexpectedValueException(
                        __CLASS__ . ' does not know how to construct [' . $elementName . ']. See ' . __CLASS__ . '::addTag'
                    );
                }
                $this->_elements[] = $this->constructElement($elementName, (array)$elementData);
            }
        }

    }

    protected function constructElement($element, array $data)
    {
        $tag = $this->_tags[$element];

        if (strstr($tag, '{{value}}')) {
            $tag = preg_replace('/{{value}}/', $data['value'], $tag);
            unset($data['value']);
        }

        return preg_replace_callback('/{{data}}/', function ($matches) use ($data) {
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
