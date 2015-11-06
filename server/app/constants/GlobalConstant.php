<?php
namespace PhalconSeed\AppConstants;

class GlobalConstant
{
    const APP_NAME = 'Phalcon Seed';
    const SECRET_KEY = 'AbjXnVHG1cgH7Z2Hazpv';
    const BANNED_TIME = 900; // 15 minutes 15 * 60
    const BATCH_REQUESTS_TIME = 10; // 10 seconds
    const SESSION_EXP = 3000;
    const WEB_CLIENT_URL = "http://localhost:1234/phalcon/frontend";
    const WEB_CLIENT_URL_HOMEPAGE = "http://localhost:1234/phalcon/frontend";
    const SUPER_ADMIN = 'superadmin';

    const DEV_ENVIRONMENT = true;

    const BDB_RETURN_CODE_SUCCESS = 100;
    const BDB_RETURN_CODE_ERROR_DUPLICATE = 201;
    const BDB_RETURN_CODE_ERROR_USERNOTVALID = 202;

    const ACTION_ALL = 'All';
    const ACTION_UPDATE = 'Update';
    const ACTION_CREATE = 'Create';
    const ACTION_DELETE = 'Delete';
    const ACTION_ACTIVE = 'Active';
    const ACTION_OTHER = 'Other';
    const ACTION_CLAIMED = 'Claimed';
    const ACTION_CANCELED = 'Canceled';
    const ACTION_USED = 'Used';

    //EMAIL Configuration
    const EMAIL_NO_REPLY_ADDRESS = '';
    const EMAIL_NO_REPLY_TITLE = '';
    const EMAIL_NO_REPLY_PASSWORD = '';
    const EMAIL_NO_REPLY_HOST = '';
    const EMAIL_NO_REPLY_PORT = '';
    const EMAIL_NO_REPLY_SMTP_SECURE = 'ssl';
    const EMAIL_WELCOME_SUBJECT = 'PhalconSeed Welcome';

    // use to check input
    public static $regExpArray = array(
        "username" => "(^[A-Za-z_.@0-9]{1,20}\$)",
        "password" => "(^.{1,}\$)",
        "role_name" => "(^[A-Za-z_.@0-9]{1,20}\$)",
    );

    public static function unique_md5()
    {
        mt_srand(microtime(true) * 100000 + memory_get_usage(true));
        return md5(uniqid(mt_rand(), true));
    }

    public static function randMixed($length)
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        $size = strlen($chars);
        for ($i = 0; $i < $length; $i++) {
            $str .= $chars[rand(0, $size - 1)];
        }
        return $str;
    }

    public static function randNumber($length)
    {
        $chars = "0123456789";
        $str = "";
        $size = strlen($chars);
        for ($i = 0; $i < $length; $i++) {
            $str .= $chars[rand(0, $size - 1)];
        }
        return $str;
    }

    public static function randAlphabet($length)
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $str = "";
        $size = strlen($chars);
        for ($i = 0; $i < $length; $i++) {
            $str .= $chars[rand(0, $size - 1)];
        }
        return $str;
    }

    public static function sendMail($toEmail, $subject, $content)
    {
        $CharSet = 'utf-8';
        $message = $content;

        $mail = new \PHPMailer();

        // SMTP enabled
        $mail->IsSMTP();

        // 1 = Message + ERROR
        // 2 = Message
        $mail->SMTPDebug = 1; // Debug when connecting SMTP server


        $mail->SMTPAuth = true;
        $mail->SMTPSecure = self::EMAIL_NO_REPLY_SMTP_SECURE;
        $mail->Host = self::EMAIL_NO_REPLY_HOST;
        $mail->Port = self::EMAIL_NO_REPLY_PORT;
        $mail->Username = self::EMAIL_NO_REPLY_ADDRESS;
        $mail->Password = self::EMAIL_NO_REPLY_PASSWORD;

        // From Address
        $mail->SetFrom(self::EMAIL_NO_REPLY_ADDRESS, self::EMAIL_NO_REPLY_TITLE);

        // To Address
        $mail->AddAddress($toEmail);

        // Reply Address
        $mail->AddReplyTo(self::EMAIL_NO_REPLY_ADDRESS);

        // Attachment
        // $mail->AddAttachment($attachment);

        // Thiet lap tieu de
        $mail->Subject = $subject;

        // Thiet lap charset
        $mail->CharSet = $CharSet;

        // Thiet lap noi dung
        $body = $message;

        //$mail->Body = $body;
        $mail->MsgHTML($body);
        if ($mail->Send() == false) {
            return false;
        } else {
            return true;
        }
    }

    public static function timezone_offset_string()
    {
        $offset = timezone_offset_get(new \DateTimeZone(ini_get('date.timezone')), new \DateTime());
        return sprintf("%s%02d:%02d", ($offset >= 0) ? '+' : '-', abs($offset / 3600), abs($offset % 3600));
    }

    public static function convert_from_another_time($source, $source_timezone, $dest_timezone)
    {
        $offset = $dest_timezone - $source_timezone;
        if ($offset == 0)
            return $source;
        $target = new \DateTime($source);
        $target->format('Y-m-d H:i:s');
        $target->modify($offset . ' hours');
        return $target;
    }
}