<?php
// If your hosting does not support per minutes cron, you can run this fallback cron and set this daily.
require __DIR__ . '/headless.php';
$cron_log = new CronLog();
$cron_log->date = date('Y-m-d');
$logs = date('D M Y h:i A') . PHP_EOL;
$logs .= '_______________________________________';
Invoice::generateRecurringInvoices($settings, $_L);

$send_payment_reminder = $settings['task_automatic_payment_reminder'] ?? false;
$task_daily_accounting_snapshot = $settings['task_daily_accounting_snapshot'] ?? false;

if ($send_payment_reminder == '1') {
    Invoice::sendPaymentReminder($settings, $_L);
}


if($task_daily_accounting_snapshot == '1'){
    $accounting_snapshot = Account::createSnapshot($settings, $_L, true);
}

$cron_log->logs = $logs . PHP_EOL . $cron_log->logs;

$cron_log->save();

api_response([
    'success' => true,
]);
