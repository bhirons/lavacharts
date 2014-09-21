<?php namespace Lavacharts\Tests\Configs;

use \Lavacharts\Tests\ProvidersTestCase;
use \Lavacharts\Configs\VerticalAxis;

class VerticalAxisTest extends ProvidersTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->va = new VerticalAxis(array());

        $this->mockTextStyle = $this->getMock(
            '\Lavacharts\Configs\TextStyle',
            array('__construct')
        );
    }

    public function testConstructorDefaults()
    {
        $this->assertNull($this->va->baselineColor);
        $this->assertNull($this->va->textStyle);
        $this->assertNull($this->va->format);
    }

    public function testConstructorValuesAssignment()
    {
        $mockTextStyle = $this->getMock(
            '\Lavacharts\Configs\TextStyle',
            array('__construct')
        );

        $a = new VerticalAxis(array(
            'baselineColor' => '#F4D4E7',
            'textStyle'     => $this->mockTextStyle,
            'format'        => '999.99'
        ));

        $this->assertEquals('#F4D4E7', $a->baselineColor);
        $this->assertTrue(is_array($a->textStyle));
        $this->assertEquals('999.99', $a->format);
    }

    /**
     * @expectedException Lavacharts\Exceptions\InvalidConfigProperty
     */
    public function testConstructorWithInvalidPropertiesKey()
    {
        new VerticalAxis(array('Jellybeans' => array()));
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testBaselineColorWithBadParams($badVals)
    {
        $this->va->baselineColor($badVals);
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testTextStyleWithBadParams()
    {
        $this->va->textStyle('not a TextStyle object');
    }

    /**
     * @dataProvider nonStringProvider
     * @expectedException Lavacharts\Exceptions\InvalidConfigValue
     */
    public function testFormatWithBadParams($badVals)
    {
        $this->va->format($badVals);
    }

}