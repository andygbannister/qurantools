<?php
/**
 * Used for testing email in unit tests
 * @see https://codeception.com/12-15-2013/testing-emails-in-php for the guts of this
 *
 * This page helped me figure out how to set up unit test helpers for PHPUnit
 * @see https://dzone.com/articles/practical-php-testing/practical-php-testing-patterns-51
 */
class EmailHelper extends \Codeception\Test\Unit
{
    /**
     * @var \GuzzleHttp\Client
     */
    private $mailcatcher;

    public function __construct(\PHPUnit\Framework\TestCase $testCase)
    {
        $this->testCase = $testCase;
        
        // Get mailcatcher settings from config
        $config      = \Codeception\Configuration::config();
        $apiSettings = \Codeception\Configuration::suiteSettings('unit', $config);
        $base_uri    = $apiSettings['modules']['config']['MailCatcher']['url'] . ':' . $apiSettings['modules']['config']['MailCatcher']['port'];

        // This is the IP and port of where we can see the mails that mailcatcher is intercepting
        // i.e. you should be able to browse to this location
        // $this->mailcatcher = new \GuzzleHttp\Client(['base_uri' => 'http://127.0.0.1:8025']);
        $this->mailcatcher = new \GuzzleHttp\Client(['base_uri' => $base_uri]);

        // clean emails between tests
        $this->cleanMessages();
    }

    // api calls
    public function cleanMessages()
    {
        $this->mailcatcher->delete('/messages');
    }

    public function getLastMessage()
    {
        $messages = $this->getMessages();
        if (empty($messages))
        {
            $this->fail("No messages received");
        }
        // messages are in descending order
        return reset($messages);
    }

    public function getMessages()
    {
        // $jsonResponse = $this->mailcatcher->request('GET','/messages');
        $jsonResponse = $this->mailcatcher->get('/messages');
        return json_decode($jsonResponse->getBody());
    }

    // assertions
    public function assertEmailIsSent($description = '')
    {
        $this->assertNotEmpty($this->getMessages(), $description);
    }

    public function assertEmailSubjectContains($needle, $email, $description = '')
    {
        $this->assertContains($needle, $email->subject, $description);
    }

    public function assertEmailSubjectEquals($expected, $email, $description = '')
    {
        $this->assertContains($expected, $email->subject, $description);
    }

    public function assertEmailHtmlContains($needle, $email, $description = '')
    {
        $response = $this->mailcatcher->get("/messages/{$email->id}.html");
        $this->assertContains($needle, (string) $response->getBody(), $description);
    }

    public function assertEmailTextContains($needle, $email, $description = '')
    {
        $response = $this->mailcatcher->get("/messages/{$email->id}.plain");
        $this->assertContains($needle, (string) $response->getBody(), $description);
    }

    public function assertEmailSenderEquals($expected, $email, $description = '')
    {
        $response = $this->mailcatcher->get("/messages/{$email->id}.json");
        $email    = json_decode($response->getBody());
        $this->assertEquals($expected, $email->sender, $description);
    }

    public function assertEmailRecipientsContain($needle, $email, $description = '')
    {
        $response = $this->mailcatcher->get("/messages/{$email->id}.json");
        $email    = json_decode($response->getBody());
        $this->assertContains($needle, $email->recipients, $description);
    }
}
