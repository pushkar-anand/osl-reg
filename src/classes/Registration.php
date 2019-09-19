<?php

use EasyMail\Mail;
use PhpUseful\EasyHeaders;
use PhpUseful\MySQLHelper;

require_once __DIR__ . "/../../vendor/autoload.php";

class Registration
{
    const TABLE_NAME = 'OSD_REGISTRATIONS';
    const COL_ID = 'reg_id';
    const COL_NAME = 'name';
    const COL_EMAIL = 'email';
    const COL_PHONE = 'phone';
    const COL_PACKAGE = 'package';
    const COL_CHECKED_IN = 'checked_in';
    const COL_TIMESTAMP = 'REG_TIMESTAMP';

    private $helper;

    private $name;
    private $email;
    private $phone;
    private $package;


    /**
     * Registration constructor.
     * @param string $name
     * @param string $email
     * @param string $phone
     * @param string $package
     * @throws Exception
     */
    public function __construct(string $name, string $email, string $phone, string $package)
    {
        global $logger;
        try {
            $this->helper =
                new MySQLHelper(DB_SERVER, DB_NAME, DB_PASSWORD, DB_USER);

            $this->name = $name;
            $this->email = $email;
            $this->phone = $phone;
            $this->package = $package;

        } catch (Exception $exception) {
            $logger->critical('DATABASE ERROR: ' . $exception->getMessage());
            EasyHeaders::service_unavailable();
        }

        if (!$this->isEmailValid($email)) {
            throw new Exception("Email address is not valid.");
        }

        if ($this->userExists($email)) {
            throw new Exception("Email is already registered.");
        }


    }

    private function isEmailValid(string $input): bool
    {
        return filter_var($input, FILTER_VALIDATE_EMAIL) !== false;
    }

    private function userExists(string $email): bool
    {
        $count =
            $this->helper->getResultCount(self::TABLE_NAME, self::COL_EMAIL, $email);
        return ($count > 0);
    }

    public function saveDetails()
    {
        global $logger;
        $fields = array(self::COL_NAME, self::COL_EMAIL, self::COL_PHONE, self::COL_TIMESTAMP);

        try {
            $this->helper->insert(self::TABLE_NAME, $fields, 'sss',
                $this->name, $this->email, $this->phone, $this->package);
            $this->sendConfirmationEmail();
        } catch (Exception $e) {
            $logger->error('ERROR SAVING: ' . $e->getMessage());
        }
    }

    private function sendConfirmationEmail()
    {
        $from = "confirmation@osl";
        $reply = 'no-reply@osl';

        $subject = "OSL Open Source Day: Confirmation";

        try {

            $mail = new Mail($this->email, $subject);
            $mail->addReplyTo($reply, "OSL");
            $mail->setFrom($from, "OSL VVCE");
            $mail->isHtml(true);

            $body = <<<MAIL

MAIL;
            $mail->setMsg($body);
            $mail->sendMail();

        } catch (Exception $e) {

        }

    }

}