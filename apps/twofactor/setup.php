<?php
// Copyright (c) 2016 Sadia Sharmin

$action = route(2);

$ui->assign('selected_navigation', 'settings');
$ui->assign('_title', 'OTP' . ' - ' . $config['CompanyName']);
$ui->assign('_st', 'OTP');
$user = User::_info();
$ui->assign('user', $user);

require 'apps/twofactor/vendor/autoload.php';

switch ($action) {
    case 'init':
        view('app_wrapper', [
            '_include' => 'init',
        ]);

        break;

    case 'enable':
        $authenticator = new PHPGangsta_GoogleAuthenticator();

        $secret = $user->pin;

        if (strlen($secret) != 16) {
            $secret = $authenticator->createSecret();
            $user->pin = $secret;
            $user->save();
        }

        $title = $config['CompanyName'];
        $title = str_replace(' ', '-', $title);
        $title = str_replace(',', '', $title);
        $title = str_replace('.', '', $title);
        $title = str_replace('\'', '', $title);
        $qrCodeUrl = $authenticator->getQRCodeGoogleUrl(
            $title,
            $secret,
            APP_URL
        );
        $ui->assign('qr_code', $qrCodeUrl);

        view('app_wrapper', [
            '_include' => 'enable',
        ]);

        break;

    case 'enable_verify':
        $authenticator = new PHPGangsta_GoogleAuthenticator();

        $secret = $user->pin; //This is used to generate QR code
        $otp = _post('otp');

        $tolerance = 1;
        //Every otp is valid for 30 sec.
        // If somebody provides OTP at 29th sec, by the time it reaches the server OTP is expired.
        //So we can give tolerance =1, it will check current  & previous OTP.
        // tolerance =2, verifies current and last two OTPS

        $checkResult = $authenticator->verifyCode($secret, $otp, $tolerance);

        if ($checkResult) {
            $msg = 'OTP is Validated and Enabled Succesfully.';

            $user->otp = 'Yes';
            $user->save();

            r2(U . 'twofactor/setup/init/', 's', $msg);
        } else {
            $msg = 'OTP verification Failed';
            r2(U . 'twofactor/setup/enable/', 'e', $msg);
        }

        break;

    case 'disable':
        $msg = 'OTP is Disabled Succesfully.';

        $user->otp = 'No';
        $user->save();

        r2(U . 'twofactor/setup/init/', 's', $msg);

        break;

    default:
        echo 'action not defined';
}
