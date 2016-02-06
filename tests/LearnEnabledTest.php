<?php

require_once 'BaseTestCase.php';

class LearnEnabledTest extends BaseTestCase
{
    public function setUp()
    {
        if (!isset($GLOBALS['PHPUNIT_SA_LEARN_ENABLED'])
            || $GLOBALS['PHPUNIT_SA_LEARN_ENABLED'] == 0) {
            $this->markTestSkipped(
                'To test the learning behavior, enable the TELL '.
                'command in SpamAssassin and update phpunit.xml'
            );
        }

        /* @see phpunit.xml */
        if (!empty($GLOBALS['PHPUNIT_SA_SOCKET'])) {
            $params = [
                'socketPath' => $GLOBALS['PHPUNIT_SA_SOCKET'],
                'user'       => $GLOBALS['PHPUNIT_SA_USER'],
            ];
        } else {
            $params = [
                'hostname' => $GLOBALS['PHPUNIT_SA_HOST'],
                'port'     => (int) $GLOBALS['PHPUNIT_SA_PORT'],
                'user'     => $GLOBALS['PHPUNIT_SA_USER'],
            ];
        }

        $params['protocolVersion'] = $GLOBALS['PHPUNIT_SA_PROTOCOL_VERSION'];

        $this->sa = new SpamAssassin_Client($params);
    }

    public function testShouldLearnAndMessageAsSpam()
    {
        $message = $this->getMessage('Spam_GTUBE.txt');

        $this->assertTrue($this->sa->learn($message, SpamAssassin_Client::LEARN_SPAM));
        $this->assertTrue($this->sa->learn($message, SpamAssassin_Client::LEARN_FORGET));
    }

    public function testShouldLearnMessageAsHam()
    {
        $message = $this->getMessage('Ham_testLearnMessageAsHam.txt');

        $this->assertTrue($this->sa->learn($message, SpamAssassin_Client::LEARN_HAM));
        $this->assertTrue($this->sa->learn($message, SpamAssassin_Client::LEARN_FORGET));
    }

    public function testShouldNotLearnIfMessageIsAlreadyKnown()
    {
        $message = $this->getMessage('Ham_testLearnMessageAsHam.txt');

        /* should learn in the first call */
        $this->assertTrue($this->sa->learn($message, SpamAssassin_Client::LEARN_HAM));

        /* should fail in the second call because message is already known */
        $this->assertFalse($this->sa->learn($message, SpamAssassin_Client::LEARN_HAM));

        /* cleanup (forget message) */
        $this->assertTrue($this->sa->learn($message, SpamAssassin_Client::LEARN_FORGET));
    }
}
