<?php

require_once 'BaseTestCase.php';

class ProcessTest extends BaseTestCase
{
    public function testShouldReturnProcessedMessage()
    {
        $result = $this->sa->process($this->gtube);

        $this->assertEquals(true, $result->isSpam);
        $this->assertTrue(is_float($result->score));
        $this->assertTrue(is_float($result->thresold));

        $this->assertContains(
            'Content-Description: original message before SpamAssassin',
            $result->message
        );
    }
}
