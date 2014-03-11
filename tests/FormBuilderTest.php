<?php

class FormBuilderTest extends PHPUnit_Framework_TestCase {

    protected $_formBuilder;

    public function setUp()
    {
        $this->_formBuilder = new FormBuilder;
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function it_should_throw_exception_with_invalid_json()
    {
        $this->_formBuilder->parseJson('Invalid json');
    }

    /**
     * @test
     */
    public function it_can_successfully_parse_json()
    {
        $result = $this->_formBuilder->parseJson(file_get_contents('_data/form.json'));
        $this->assertTrue($result);
    }

    /**
     * @test
     */
    public function it_contains_the_correct_data_once_parsed()
    {
        $this->_formBuilder->parseJson(file_get_contents('_data/form.json'));
        $formBuilder = $this->_formBuilder->getBlueprint();
        $this->assertObjectHasAttribute('label', $formBuilder->data->label01);
        $this->assertObjectHasAttribute('input', $formBuilder->data->input01);
        $this->assertObjectHasAttribute('textarea', $formBuilder->data->textarea01);
    }

    /**
     * @test
     * @expectedException UnexpectedValueException
     */
    public function it_should_throw_exception_with_unknown_tag()
    {
        $this->_formBuilder->parseJson(
            '{"location":"index.php","data":{"unknown_tag":{"for":"username","value":"Enter username"}}}'
        );
        $this->_formBuilder->build();
    }

    /**
     * @test
     */
    public function it_should_be_able_to_add_custom_tags()
    {
        $this->_formBuilder->parseJson(
            '{"location":"index.php","data":{"custom_tag":{"class":"custom-class-tag"}}}'
        );
        $this->_formBuilder->addTag('custom_tag', '<custom_tag {{data}}>');
        $this->assertEquals(
            array(
                'label' => '<label {{data}}>{{value}}</label>',
                'input' => '<input {{data}}>',
                'textarea' => '<textarea {{data}}>{{value}}</textarea>',
                'custom_tag' => '<custom_tag {{data}}>'
            ),
            $this->_formBuilder->getTags()
        );
    }

    /**
     * @test
     */
    public function it_should_correctly_build_a_single_element()
    {
        $this->_formBuilder->parseJson(
            '{"location":"index.php","data":{"label291031":{"label":{"for":"username","value":"Enter username"}}}}'
        );
        $this->_formBuilder->build();
        $this->assertEquals(
            array(
                "<label for=\"username\">Enter username</label>"
            ),
            $this->_formBuilder->getElements()
        );
    }

    /**
     * @test
     */
    public function it_should_correctly_build_all_elements()
    {
        $this->_formBuilder->parseJson(
            file_get_contents('_data/form.json')
        );
        $this->_formBuilder->build();
        $this->assertEquals(
            array(
                "<label for=\"username\">Enter username</label>",
                "<input type=\"text\" name=\"username\" class=\"random-input-class\" id=\"username\" placeholder=\"Please enter your username\">",
                "<label for=\"textarea\">Enter some information</label>",
                "<textarea name=\"super_awesome_textarea\" id=\"textarea\">This is a value for the textarea</textarea>"
            ),
            $this->_formBuilder->getElements()
        );
    }

}