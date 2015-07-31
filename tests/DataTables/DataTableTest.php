<?php

namespace Khill\Lavacharts\Tests\DataTables;

use \Khill\Lavacharts\DataTables\DataTable;
use \Khill\Lavacharts\Tests\ProvidersTestCase;
use Carbon\Carbon;
use \Mockery as m;

class DataTableTest extends ProvidersTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->dt = new DataTable();
    }

    public function testDefaultTimezoneUponCreation()
    {
        $this->assertEquals($this->dt->timezone, 'America/Los_Angeles');
    }

    public function testSetTimezoneWithConstructor()
    {
        $dt = new DataTable('America/New_York');

        $this->assertEquals($dt->timezone, 'America/New_York');
    }

    public function testSetTimezoneWithMethod()
    {
        $this->dt->setTimezone('America/New_York');

        $this->assertEquals($this->dt->timezone, 'America/New_York');
    }

    /**
     * @dataProvider nonStringProvider
     */
    public function testIniDefaultTimezoneWithBadValues($badValues)
    {
        date_default_timezone_set('America/Los_Angeles');

        $dt = new DataTable($badValues);

        $this->assertEquals($dt->timezone, 'America/Los_Angeles');
    }

    public function testGetColumns()
    {
        $this->dt->addColumn('date', 'Test1');
        $this->dt->addColumn('number', 'Test2');

        $cols = $this->dt->getColumns();

        $this->assertEquals($cols[0]->getType(), 'date');
        $this->assertEquals($cols[1]->getType(), 'number');
    }

    public function testGetRows()
    {
        $this->dt->addColumn('date');

        $this->dt->addRow([Carbon::parse('March 24th, 1988')]);
        $this->dt->addRow([Carbon::parse('March 25th, 1988')]);

        $rows = $this->dt->getRows();

        $this->assertEquals($rows[0]->getColumnValue(0), 'Date(1988,2,24,0,0,0)');
        $this->assertEquals($rows[1]->getColumnValue(0), 'Date(1988,2,25,0,0,0)');
    }

    /**
     * @depends testGetColumns
     */
    public function testAddColumnWithTypeOnly()
    {
        $this->dt->addColumn('date');

        $cols = $this->dt->getColumns();

        $this->assertEquals($cols[0]->getType(), 'date');
    }

    /**
     * @depends testGetColumns
     */
    public function testAddColumnWithArrayOfTypeOnly()
    {
        $this->dt->addColumn(['date']);

        $cols = $this->dt->getColumns();

        $this->assertEquals($cols[0]->getType(), 'date');
    }

    /**
     * @depends testGetColumns
     */
    public function testAddColumnWithTypeAndDescription()
    {
        $this->dt->addColumn('date', 'Days in March');

        $cols = $this->dt->getColumns();

        $this->assertEquals($cols[0]->getType(), 'date');
        $this->assertEquals($cols[0]->getLabel(), 'Days in March');
    }

    /**
     * @depends testGetColumns
     */
    public function testAddColumnWithArrayOfTypeAndDescription()
    {
        $this->dt->addColumn(['date', 'Days in March']);

        $cols = $this->dt->getColumns();

        $this->assertEquals($cols[0]->getType(), 'date');
        $this->assertEquals($cols[0]->getLabel(), 'Days in March');
    }

    /**
     * @depends testGetColumns
     */
    public function testAddMultipleColumnsWithArrayOfTypeAndDescription()
    {
        $this->dt->addColumns([
            ['date', 'Days in March'],
            ['number', 'Day of the Week'],
            ['number', 'Temperature'],
        ]);

        $cols = $this->dt->getColumns();

        $this->assertEquals($cols[0]->getType(), 'date');
        $this->assertEquals($cols[0]->getLabel(), 'Days in March');

        $this->assertEquals($cols[1]->getType(), 'number');
        $this->assertEquals($cols[1]->getLabel(), 'Day of the Week');

        $this->assertEquals($cols[2]->getType(), 'number');
        $this->assertEquals($cols[2]->getLabel(), 'Temperature');
    }

    /**
     * @depends testGetColumns
     * @depends testGetRows
     * @depends testAddColumnWithTypeOnly
     */
    public function testAddRowWithTypeDateOnly()
    {
        $this->dt->addColumn('date');

        $this->dt->addRow([Carbon::parse('March 24th, 1988')]);

        $cols = $this->dt->getColumns();
        $rows = $this->dt->getRows();

        $this->assertEquals($cols[0]->getType(), 'date');
        $this->assertEquals($rows[0]->getColumnValue(0), 'Date(1988,2,24,0,0,0)');
    }

    /**
     * @depends testGetColumns
     * @depends testGetRows
     * @depends testAddColumnWithTypeAndDescription
     */
    public function testAddRowWithMultipleColumnsWithDateAndNumbers()
    {
        $this->dt->addColumn('date');
        $this->dt->addColumn('number');
        $this->dt->addColumn('number');

        $this->dt->addRow([Carbon::parse('March 24th, 1988'), 12345, 67890]);

        $cols = $this->dt->getColumns();
        $rows = $this->dt->getRows();

        $this->assertEquals($cols[0]->getType(), 'date');
        $this->assertEquals($cols[1]->getType(), 'number');
        $this->assertEquals($rows[0]->getColumnValue(0), 'Date(1988,2,24,0,0,0)');
        $this->assertEquals($rows[0]->getColumnValue(1), 12345);
        $this->assertEquals($rows[0]->getColumnValue(2), 67890);
    }

    /**
     * @depends testGetColumns
     * @depends testGetRows
     * @depends testAddColumnWithTypeAndDescription
     */
    public function testAddMultipleRowsWithMultipleColumnsWithDateAndNumbers()
    {
        $this->dt->addColumn('date');
        $this->dt->addColumn('number');
        $this->dt->addColumn('number');

        $rows = [
            [Carbon::parse('March 24th, 1988'), 12345, 67890],
            [Carbon::parse('March 25th, 1988'), 1122, 3344]
        ];

        $this->dt->addRows($rows);

        $cols = $this->dt->getColumns();
        $rows = $this->dt->getRows();

        $this->assertEquals($cols[0]->getType(), 'date');
        $this->assertEquals($cols[1]->getType(), 'number');
        $this->assertEquals($rows[0]->getColumnValue(0), 'Date(1988,2,24,0,0,0)');
        $this->assertEquals($rows[0]->getColumnValue(1), 12345);
        $this->assertEquals($rows[0]->getColumnValue(2), 67890);
        $this->assertEquals($rows[1]->getColumnValue(0), 'Date(1988,2,25,0,0,0)');
        $this->assertEquals($rows[1]->getColumnValue(1), 1122);
        $this->assertEquals($rows[1]->getColumnValue(2), 3344);
    }

    /**
     * @depends testGetColumns
     * @depends testGetRows
     * @depends testAddColumnWithTypeAndDescription
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidRowDefinition
     */
    public function testAddBadMultipleRowsWithMultipleColumnsWithDateAndNumbers()
    {
        $this->dt->addColumn('date');
        $this->dt->addColumn('number');
        $this->dt->addColumn('number');

        $rows = [
            [Carbon::parse('March 24th, 1988'), 12345, 67890],
            234.234
        ];

        $this->dt->addRows($rows);

        $cols = $this->dt->getColumns();
        $rows = $this->dt->getRows();

        $this->assertEquals($cols[0]->getType(), 'date');
        $this->assertEquals($cols[1]->getType(), 'number');
        $this->assertEquals($rows[0]['c'][0]['v'], 'Date(1988, 2, 24, 0, 0, 0)');
        $this->assertEquals($rows[0]['c'][1]['v'], 12345);
        $this->assertEquals($rows[0]['c'][2]['v'], 67890);
        $this->assertEquals($rows[1]['c'][0]['v'], 'Date(1988, 2, 25, 0, 0, 0)');
        $this->assertEquals($rows[1]['c'][1]['v'], 1122);
        $this->assertEquals($rows[1]['c'][2]['v'], 3344);
    }

    /**
     * @depends testAddColumnWithTypeAndDescription
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidCellCount
     */
    public function testAddMoreCellsThanColumns()
    {
        $this->dt->addColumn('date');
        $this->dt->addColumn('number');
        $this->dt->addRow([Carbon::parse('March 24th, 1988'), 12345, 67890]);
    }

    /**
     * @depends testAddColumnWithTypeAndDescription
     * @dataProvider nonCarbonOrDateStringProvider
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidDateTimeString
     */
    public function testAddingRowWithBadDateTypes($badDate)
    {
        $this->dt->addColumn('date');
        $this->dt->addRow([$badDate]);
    }

    /**
     * @depends testAddColumnWithTypeAndDescription
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidRowProperty
     */
    public function testAddingRowWithEmptyArray()
    {
        $this->dt->addColumn('date');
        $this->dt->addRow([[]]);
    }

    /**
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidColumnType
     */
    public function testAddBadColumnsFromArray()
    {
        $this->dt->addColumns([
            [5, 'falcons'],
            [false, 'tacos']
        ]);
    }

    /**
     * @depends testAddColumnWithTypeAndDescription
     * @expectedException \Khill\Lavacharts\Exceptions\InvalidColumnIndex
     */
    public function testAddBadColumnFromat()
    {
        $mockDateFormat = m::mock('Khill\Lavacharts\DataTables\Formats\DateFormat');

        $this->dt->addColumn('date');
        $this->dt->formatColumn('grizzly', $mockDateFormat);
    }

     /**
     * @depends testGetColumns
     * @depends testGetRows
     * @depends testAddColumnWithTypeAndDescription
     */
    public function testAddMultipleRowsWithMultipleColumnsWithDateTimeAndNumbers()
    {
        $this->dt->addColumns([
            ['datetime'],
            ['number'],
            ['number']
        ])->addRows([
            [Carbon::parse('March 24th, 1988 8:01:05'), 12345, 67890],
            [Carbon::parse('March 25th, 1988 8:02:06'), 1122, 3344]
        ]);

        $cols = $this->dt->getColumns();
        $rows = $this->dt->getRows();

        $this->assertEquals($cols[0]->getType(), 'datetime');
        $this->assertEquals($cols[1]->getType(), 'number');

        $this->assertEquals($rows[0]->getColumnValue(0), 'Date(1988,2,24,8,1,5)');
        $this->assertEquals($rows[0]->getColumnValue(1), 12345);
        $this->assertEquals($rows[0]->getColumnValue(2), 67890);
        $this->assertEquals($rows[1]->getColumnValue(0), 'Date(1988,2,25,8,2,6)');
        $this->assertEquals($rows[1]->getColumnValue(1), 1122);
        $this->assertEquals($rows[1]->getColumnValue(2), 3344);
    }

}
