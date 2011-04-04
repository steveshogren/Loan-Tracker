<?php
class UserTest extends PHPUnit_Framework_TestCase
{
    private $_User;

    public function setUp()
    {
        parent::setUp();
        $Factory = new Factory();
        $this->_User = $Factory->buildUser();
    }

    public function test_setSortOrder_setToDefault()
    {
        $ExpectedSortOrder = new SortOrder_Default();
        $this->_User->setSortOrderTo($ExpectedSortOrder);
        $ReturnedSortOrder = $this->_User->getSortOrder();
        $this->assertEquals(
            $ExpectedSortOrder,
            $ReturnedSortOrder,
            'Expected that the SortOrder would be Default.'
        );
    }

    public function test_setSortOrder_setToSnowball()
    {
        $ExpectedSortOrder = new SortOrder_Snowball();
        $this->_User->setSortOrderTo($ExpectedSortOrder);
        $ReturnedSortOrder = $this->_User->getSortOrder();
        $this->assertEquals(
            $ExpectedSortOrder,
            $ReturnedSortOrder,
            'Expected that the SortOrder would be Snowball.'
        );
    }

}