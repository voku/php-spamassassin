<?php

require_once 'BaseTestCase.php';

class ConnectionTest extends BaseTestCase
{
    public function testPing()
    {
        $this->assertEquals(true, $this->sa->ping());
    }
}
