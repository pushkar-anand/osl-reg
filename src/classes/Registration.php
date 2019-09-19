<?php
require_once __DIR__ . "/../../vendor/autoload.php";

use EasyMail\Mail;
use PhpUseful\EasyHeaders;
use PhpUseful\MySQLHelper;


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
        try {
            $this->helper =
                new MySQLHelper(DB_SERVER, DB_USER, DB_PASSWORD, DB_NAME);

            $this->name = $name;
            $this->email = $email;
            $this->phone = $phone;
            $this->package = $package;

        } catch (Exception $exception) {
            error_log($exception->getMessage());
            getLogger()->critical('DATABASE ERROR: ' . $exception->getMessage());
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
        $fields = array(self::COL_NAME, self::COL_EMAIL, self::COL_PHONE, self::COL_PACKAGE);

        try {
            $this->helper->insert(self::TABLE_NAME, $fields, 'ssss',
                $this->name, $this->email, $this->phone, $this->package);
            $this->sendConfirmationEmail();
        } catch (Exception $e) {
            getLogger()->error('ERROR SAVING: ' . $e->getMessage());
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
<!DOCTYPE html>
<html>
<head>
    <!-- <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /> -->
    <title></title>
    <!-- <meta name="viewport" content="width=device-width, initial-scale=1.0" /> -->
</head>

<body style="margin: 0; padding: 0;">
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td style="padding: 10px 0 30px 0;">
                <table align="center" border="0" cellpadding="0" cellspacing="0" width="600"
                    style="border: 1px solid #cccccc; border-collapse: collapse;">
                    <tr>
                        <td align="center" bgcolor="#000000"
                            style="padding: 40px 0 30px 0; color: #ffffff; font-size: 28px; font-weight: bold; font-family: Arial, sans-serif;">
                            <img src=""
                                alt="Open Source Day" width="400" height="100"
                                 style="display: block; font-size:40px;" />
                        </td>
                    </tr>
                    <tr>
                        <td bgcolor="#ffffff" style="padding: 40px 30px 40px 30px;">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td style="color: #153643; font-family: Arial, sans-serif; font-size: 24px;">
                                        <b>Hello! {$this->name} </b>
                                        <br>
                                        <div style="font-size : 20px; margin-top: 5%; ">
                                                Thank you for registering for Open Source Day, <br>
                                                This email is your confirmation,
                                                Please head over to OSL at A-block Ground floor to pay registration fee of Rs.100 to confirm your seat.
                                        </div>
                                        
                                    </td>
                                </tr>
                                <tr>
                                    <td
                                        style="padding: 20px 0 30px 0; color: #153643; font-family: Arial, sans-serif; font-size: 20px; line-height: 20px; font-weight:bold;">
                                        <p>Selected Package</p>
                                        <p style="font-size: 16px;">Selected Package: {$this->package}</p>
                                        

                                    </td>

                                    
                                </tr>
                               
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td bgcolor="#000000" style="padding: 30px 30px 30px 30px;">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td style="color: #ffffff; font-family: Arial, sans-serif; font-size: 14px;"
                                        width="75%">
                                        &reg;Osl vvce<br />
                                        <a href="#" style="color: #ffffff;">
                                            <font color="#ffffff">Unsubscribe</font>
                                        </a> 
                                    </td>
                                   
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
MAIL;
            $mail->setMsg($body);
            $mail->sendMail();

        } catch (Exception $e) {

        }

    }

}