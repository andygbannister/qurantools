<?php

namespace QT\Mail;

// $this->markTestIncomplete('This test has not been implemented yet.');

include_once 'library/mail_functions.php';
include_once 'auth/auth_functions.php';
require_once 'tests/unit/EmailHelper.php';

use AspectMock\Test as test;
use DMS\PHPUnitExtensions\ArraySubset\Assert; // see https://github.com/sebastianbergmann/phpunit/issues/3494

class MailFunctionsTest extends \Codeception\Test\Unit
{
    public $user;
    public $user_name = 'Test Man';
    public $payment_intent;

    protected function _before()
    {
        $this->config = \Codeception\Configuration::config();
        $apiSettings  = \Codeception\Configuration::suiteSettings(
            'acceptance_phpbrowser',
            $this->config
        );
        $this->main_app_url = $apiSettings['modules']['enabled'][0]['PhpBrowser']['url'];

        global $config;
        $config['main_app_url'] = $this->main_app_url;
    }

    protected function _after()
    {
        test::clean(); // remove all registered test doubles
    }

    /**
     * @var \UnitTester
     */
    protected $tester;

    // generate_password_reset_email_HTML
    public function testGeneratePasswordResetEmailHtml(): void
    {
        $reset_link   = "http://example.com";
        $html_message = generate_password_reset_email_HTML(
            $this->user_name,
            $reset_link
        );

        $this->assertStringContainsString(
            "Dear " . $this->user_name,
            $html_message
        );
        $this->assertStringContainsString("href='$reset_link'", $html_message);
    }

    // process_password_reset_email
    public function testProcessGoodResetEmail(): void
    {
        $to           = "bob@example.com";
        $html_message = "<p>Dear Bob, check this out</p>";
        $this->assertTrue(process_password_reset_email($to, $html_message));
    }

    /**
     * @group needs_email_server
     */
    public function testProcessGoodResetEmailContent(): void
    {
        $this->emailHelper = new \EmailHelper($this);

        $to           = "bob@example.com";
        $html_message = "<p>Dear Bob, check this out</p>";
        process_password_reset_email($to, $html_message);

        $email = $this->emailHelper->getLastMessage();
        $this->emailHelper->assertEmailSenderEquals(
            "<" . QT_ADMIN_EMAIL . ">",
            $email
        );
        $this->emailHelper->assertEmailRecipientsContain("<$to>", $email);
        $this->emailHelper->assertEmailSubjectEquals(
            PASSWORD_RESET_SUBJECT,
            $email
        );
        $this->emailHelper->assertEmailHtmlContains($html_message, $email);
    }

    public function testProcessBadResetEmailNoEmail(): void
    {
        $to           = "bob@example.com";
        $html_message = "<p>Dear Bob, check this out</p>";
        $this->assertFalse(process_password_reset_email("", $html_message));
    }

    public function testProcessBadResetEmailArgs(): void
    {
        $to           = "bob@example.com";
        $html_message = "<p>Dear Bob, check this out</p>";
        $this->assertFalse(process_password_reset_email("", ""));
    }

    // email_error_to_qt
    public function testEmail_error_to_qtThrowsWhenNoErrorOrExtraMessage(): void
    {
        $this->expectExceptionMessage(
            'email_error_to_qt() requires either an Exception or a text error message.'
        );

        $result = email_error_to_qt();
    }

    public function testEmail_error_to_qtCallsMail(): void
    {
        $mail_mock = test::func('QT\Mail', 'mail', 'foo'); //

        $exception = new \Exception('bad stuff');

        $result = email_error_to_qt($exception);

        $mail_mock->verifyInvoked();
    }

    public function testEmail_error_to_qtEmailIsSentToTheRightPeople(): void
    {
        global $config;

        $developer_email_1          = 'bob-dev@example.com';
        $developer_email_2          = 'mary-dev@example.com';
        $config['qt_developers'][0] = $developer_email_1;
        $config['qt_developers'][1] = $developer_email_2;
        $mail_mock                  = test::func('QT\Mail', 'mail', 'foo');
        $exception                  = new \Exception('bad stuff');

        $result = email_error_to_qt($exception);

        $mail_mock->verifyInvoked(["$developer_email_1,$developer_email_2"]);
    }

    public function testEmail_error_to_qtEmailIsSetsSubject(): void
    {
        global $config;
        $subject = 'Scary Email';

        $mail_mock = test::func('QT\Mail', 'mail', 'foo'); //

        $exception = new \Exception('bad stuff');

        $result = email_error_to_qt($exception, null, $subject);

        $mail_mock->verifyInvoked([
            \join(',', $config['qt_developers']),
            $subject
        ]);
    }

    public function testEmail_error_to_qtHasADefaultSubject(): void
    {
        global $config;
        $default_subject = SUBJECT_QT_APPLICATION_ERROR;
        $mail_mock       = test::func('QT\Mail', 'mail', 'foo'); //
        $exception       = new \Exception('bad stuff');

        $result = email_error_to_qt($exception);

        $mail_mock->verifyInvoked([
            \join(',', $config['qt_developers']),
            $default_subject
        ]);
    }

    public function testEmail_error_to_qtEmailIncludesErrorDumpIfPresent(): void
    {
        // TODO: instead of using a mock, just send the email locally and test
        // the contents using the email reading test stuff

        $this->markTestIncomplete(
            'AspectMock doesn\'t allow much cleverness in matching partial values, so it\'s not easily possible to check that mail was called with something containing \'bad stuff\' as opposed to everything it was called with'
        );

        global $config;
        $default_subject = SUBJECT_QT_APPLICATION_ERROR;
        $mail_mock       = test::func('QT\Mail', 'mail', 'foo'); //
        $exception       = new \Exception('bad stuff');

        $result = email_error_to_qt($exception);

        $mail_mock->verifyInvoked([
            \join(',', $config['qt_developers']),
            $default_subject,
            'bad stuff'
        ]);
    }

    public function testEmail_error_to_qtEmailIncludesExtraMessageIfPresent(): void
    {
        // TODO: instead of using a mock, just send the email locally and test
        // the contents using the email reading test stuff

        $this->markTestIncomplete(
            'AspectMock doesn\'t allow much cleverness in matching partial values, so it\'s not easily possible to check that mail was called with something containing \'Some kind of error message\' as opposed to everything it was called with'
        );

        $extra_message = 'Some kind of error message';
        $mail_mock     = test::func('QT\Mail', 'mail', 'foo');

        $result = email_error_to_qt(null, $extra_message);

        $mail_mock->verifyInvoked([
            \join(',', $config['qt_developers']),
            $default_subject,
            'of error message'
        ]);
    }

    public function testEmail_error_to_qtEmailIncludesErrorAndExtraMessage(): void
    {
        // TODO: instead of using a mock, just send the email locally and test
        // the contents using the email reading test stuff

        $this->markTestIncomplete(
            'AspectMock doesn\'t allow much cleverness in matching partial values, so it\'s not easily possible to check that mail was called with something containing \'Some kind of error message\' as opposed to everything it was called with'
        );

        $extra_message = 'Some kind of error message';
        $mail_mock     = test::func('QT\Mail', 'mail', 'foo');
        $exception     = new \Exception('bad stuff');

        $result = email_error_to_qt($exception, $extra_message);

        $mail_mock->verifyInvoked([
            \join(',', $config['qt_developers']),
            $default_subject,
            'bad stuff and of error message'
        ]);
    }

    public function testEmail_error_to_qtDoAnActualSend(): void
    {
        // This test actually hits the mail server so that you can eyeball the
        // resulting email wherever it is sent.
        // If you are running on docker, try: http://0.0.0.0:8025/

        global $config;
        $this->emailHelper = new \EmailHelper($this);

        $subject       = 'cod and chips';
        $extra_message = 'Some kind of error message';
        $exception     = new \Exception('bad stuff');

        $result = email_error_to_qt($exception, $extra_message, $subject);

        $email = $this->emailHelper->getLastMessage();
        $this->emailHelper->assertEmailRecipientsContain(
            '<' . QT_ADMIN_EMAIL . '>',
            $email
        );

        $this->emailHelper->assertEmailSubjectEquals($subject, $email);
    }

    // email_qt
    public function testEmail_qtThrowsWhenNoMessage(): void
    {
        $message = null;

        $this->expectExceptionMessage('Argument 1 passed to QT\Mail\email_qt() must be of the type string, null given');

        $result = email_qt($message);
    }

    public function testEmail_qtEmailIsSentToQtAdmin(): void
    {
        // This test actually hits the mail server so that you can eyeball the
        // resulting email wherever it is sent.
        // If you are running on docker, try: http://0.0.0.0:8025/
        // or check the docs for mailcatcher

        $this->emailHelper = new \EmailHelper($this);

        $message = 'Hi QT';

        $result = email_qt($message);

        $email = $this->emailHelper->getLastMessage();
        $this->emailHelper->assertEmailRecipientsContain(
            '<' . QT_ADMIN_EMAIL . '>',
            $email
        );
    }

    public function testEmail_qtSetsSubject(): void
    {
        // This test actually hits the mail server so that you can eyeball the
        // resulting email wherever it is sent.
        // If you are running on docker, try: http://0.0.0.0:8025/

        global $config;
        $this->emailHelper = new \EmailHelper($this);

        $subject = 'Message from QT';
        $message = 'Hi QT';

        $result = email_qt($message, $subject);

        $email = $this->emailHelper->getLastMessage();
        $this->emailHelper->assertEmailSubjectEquals($subject, $email);
    }

    public function testEmail_qtSetsDefaultSubject(): void
    {
        global $config;
        $this->emailHelper = new \EmailHelper($this);

        $default_subject = SUBJECT_MESSAGE_FROM_QT_SYSTEM;
        $message         = 'Hi QT';

        $result = email_qt($message);

        $email = $this->emailHelper->getLastMessage();
        $this->emailHelper->assertEmailSubjectEquals($default_subject, $email);
    }

    public function testEmail_qtEmailContainsMessage(): void
    {
        $this->emailHelper = new \EmailHelper($this);

        $message = 'Hi QT';

        $result = email_qt($message);

        $email = $this->emailHelper->getLastMessage();
        $this->emailHelper->assertEmailHtmlContains($message, $email);
    }
}
