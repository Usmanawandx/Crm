<?php
// Copyright (c) 2016 Sadia Sharmin

$action = route(2, 'verify');

require 'apps/twofactor/vendor/autoload.php';

switch ($action) {
    case 'verify':
        if (!isset($_SESSION['tuid'])) {
            exit();
        }

        $ui->assign(
            'xheader',
            '    <style type="text/css">
        body {

            background-color: #FAFAFA;
            overflow-x: visible;
        }
        .paper {
            margin: 50px auto;

            border: 2px solid #DDD;
            background-color: #FFF;
            position: relative;
            width: 400px;
        }

    </style>'
        );

        $ui->assign('_include', 'verify');

        $ui->display('wrapper_content.tpl');

        break;

    case 'post':
        if (!isset($_SESSION['tuid'])) {
            exit();
        }

        $id = $_SESSION['tuid'];

        $user = db_find_one('sys_users', $id);

        if ($user) {
            $authenticator = new PHPGangsta_GoogleAuthenticator();

            $secret = $user->pin; //This is used to generate QR code
            $otp = _post('otp');

            $tolerance = 1;
            //Every otp is valid for 30 sec.
            // If somebody provides OTP at 29th sec, by the time it reaches the server OTP is expired.
            //So we can give tolerance =1, it will check current  & previous OTP.
            // tolerance =2, verifies current and last two OTPS

            $checkResult = $authenticator->verifyCode(
                $secret,
                $otp,
                $tolerance
            );

            if ($checkResult) {
                $_SESSION['uid'] = $user->id;
                $user->last_login = date('Y-m-d H:i:s');

                if (strlen($user->autologin) > 20) {
                    $str = $user->autologin;
                } else {
                    $str = Misc::random_string(20) . $user->id;
                }

                $user->autologin = $str;

                $user->save();
                //login log

                setcookie('ib_at', $str, time() + 86400 * 180, "/");

                _log(
                    $_L['Login Successful'] . ' ' . $user->username,
                    'Admin',
                    $user->id
                );

                r2($_COOKIE['ib_rd']);
            } else {
                $msg = 'OTP verification Failed';
                r2(U . 'twofactor/otp/', 'e', $msg);
            }
        } else {
            echo 'User Not Found.';
        }

        break;

    default:
        echo 'action not defined';
}
