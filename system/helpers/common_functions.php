<?php
/**
 * @param string $l
 * @return bool|string
 */
function _raid($l = '6')
{
    return substr(str_shuffle(str_repeat('0123456789', $l)), 0, $l);
}

function sp_random_number($length = 6)
{
    return substr(str_shuffle(str_repeat('0123456789', $length)), 0, $length);
}

function current_number_would_be($string)
{
    $next_code = preg_replace("/[^0-9]/", '', $string);
    $next_code = $next_code == '' ? 0 : (int) $next_code;

    return $next_code + 1;
}

/**
 * @param $option
 * @param $value
 * @return bool
 */
function update_option($option, $value)
{
    $config_exist = AppConfig::where('setting', $option)->first();

    if ($config_exist) {
        $config_exist->value = $value;
        $config_exist->save();
    } else {
        $config_create = new AppConfig();
        $config_create->setting = $option;
        $config_create->value = $value;
        $config_create->save();
    }
    return true;
}

function remove_option($option)
{
    $config_exist = AppConfig::where('setting', $option)->first();

    if ($config_exist) {
        $config_exist->delete();
    }
    return true;
}

function sp_recursive_delete_contents($directory, $with_the_directory = false)
{
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator(
            $directory,
            RecursiveDirectoryIterator::SKIP_DOTS
        ),
        RecursiveIteratorIterator::CHILD_FIRST
    );

    foreach ($files as $fileinfo) {
        $todo = $fileinfo->isDir() ? 'rmdir' : 'unlink';
        $todo($fileinfo->getRealPath());
    }

    if ($with_the_directory) {
        rmdir($directory);
    }
}

function sp_recursive_copy($source_directory, $destination_directory)
{
    $dir = opendir($source_directory);

    if (!file_exists($destination_directory) && (!mkdir($destination_directory) && !is_dir($destination_directory))) {
        throw new \RuntimeException(
            sprintf(
                'Directory "%s" was not created',
                $destination_directory
            )
        );
    }

    while (false !== ($file = readdir($dir))) {
        if ($file !== '.' && $file !== '..') {
            if (is_dir($source_directory . '/' . $file)) {
                sp_recursive_copy(
                    $source_directory . '/' . $file,
                    $destination_directory . '/' . $file
                );
            } else {
                copy(
                    $source_directory . '/' . $file,
                    $destination_directory . '/' . $file
                );
            }
        }
    }
    closedir($dir);
}

function sp_create_directory($directory)
{
    if (!file_exists($directory) && (!mkdir($concurrentDirectory = $directory) &&
    !is_dir($concurrentDirectory))) {
        throw new \RuntimeException(
            sprintf('Directory "%s" was not created', $concurrentDirectory)
        );
    }
}

function sp_create_index_file_to_prevent_directory_listing($destination)
{
    file_put_contents(
        $destination . '/index.html',
        '<html>
<head>
    <title>403 Forbidden</title>
</head>
<body>
<p>Directory access is forbidden.</p>
</body>
</html>
'
    );
}

function sp_if_the_value_is_bollean($data, $param)
{
    if (!empty($data[$param])) {
        return 1;
    }
    return 0;
}

function sp_clean_and_create_line_json($values)
{
    $result = [];
    foreach ($values as $line) {
        $line = trim($line);
        if ($line !== '' && $line !== '0') {
            $result[] = $line;
        }
    }

    return json_encode($result);
}

function replace_in_file($file_path, $old_string, $new_string): array
{
    $result = [
        'success' => false,
        'message' => '',
    ];
    if (file_exists($file_path)) {
        if (is_writable($file_path)) {
            try {
                $file_content = file_get_contents($file_path);
                $file_content = str_replace(
                    $old_string,
                    $new_string,
                    $file_content,
                    $count
                );
                if ($count === 0) {
                    $result['message'] = 'The string is not found';
                } elseif (file_put_contents($file_path, $file_content) > 0) {
                    $result['success'] = true;
                } else {
                    $result['message'] = 'Error while writing file';
                }
            } catch (Exception $e) {
                $result['message'] = 'Error : ' . $e->getMessage();
            }
        } else {
            $result['message'] = 'File ' . $file_path . ' is not writable !';
        }
    } else {
        $result['message'] = 'File ' . $file_path . ' does not exist !';
    }
    return $result;
}

/**
 * @param $number
 * @return bool
 */
function numberIsNegative($number)
{
    return $number < 0;
}

/**
 * @param $amount
 * @param $currency_iso_code
 * @return string
 */
function formatCurrency($amount, $currency_iso_code = false, $override = [])
{
    $negative = numberIsNegative($amount);
    $currency = Currency::getAllCurrencies();

    global $config;

    if (!$currency_iso_code) {
        $currency_iso_code = $config['home_currency'];
    }

    if (isset($currency[$currency_iso_code])) {
        $selected_currency = $currency[$currency_iso_code];

        $precision = $selected_currency['precision'];

        if ($config['currency_decimal_digits'] == 'false') {
            $precision = 0;
        }

        if (isset($override['precision'])) {
            $precision = $override['precision'];
        }

        $amount = (float) $amount;



        $value = number_format(
            $amount,
            $precision,
            $selected_currency['decimal_mark'],
            $selected_currency['thousands_separator']
        );

        if (!$selected_currency['symbol_first']) {
            $prefix = '';
            $suffix = $selected_currency['symbol'];
        } else {
            $prefix = $selected_currency['symbol'];
            $suffix = '';
        }

        if (isset($override['prefix'])) {
            $prefix = $override['prefix'];
        }

        if (isset($override['suffix'])) {
            $prefix = $override['suffix'];
        }

        return ($negative ? '-' : '') . $prefix . $value . $suffix;
    }

    return $amount;
}

function formatCurrencyNonGlobalConfig(
    $config,
    $amount,
    $currency_iso_code = false,
    $override = []
) {
    $negative = numberIsNegative($amount);
    $currency = Currency::getAllCurrencies();

    if (!$currency_iso_code) {
        $currency_iso_code = $config['home_currency'];
    }

    if (isset($currency[$currency_iso_code])) {
        $selected_currency = $currency[$currency_iso_code];

        $precision = $selected_currency['precision'];

        if ($config['currency_decimal_digits'] == 'false') {
            $precision = 0;
        }

        if (isset($override['precision'])) {
            $precision = $override['precision'];
        }

        $value = number_format(
            $amount,
            $precision,
            $selected_currency['decimal_mark'],
            $selected_currency['thousands_separator']
        );

        if (!$selected_currency['symbol_first']) {
            $prefix = '';
            $suffix = $selected_currency['symbol'];
        } else {
            $prefix = $selected_currency['symbol'];
            $suffix = '';
        }

        if (isset($override['prefix'])) {
            $prefix = $override['prefix'];
        }

        if (isset($override['suffix'])) {
            $prefix = $override['suffix'];
        }

        return ($negative ? '-' : '') . $prefix . $value . $suffix;
    }

    return $amount;
}

function sp_random_strings($length_of_string)
{
    // String of all alphanumeric character
    $str_result = '123456789ABCDEFGHKMNPQRSTUVWXYZabcdefghkmnpqrstuvwxyz';

    // Shufle the $str_result and returns substring
    // of specified length
    return substr(str_shuffle($str_result), 0, $length_of_string);
}

function sp_string_remove_all_between($beginning, $end, $string)
{
    $beginningPos = strpos($string, $beginning);
    $endPos = strpos($string, $end);
    if ($beginningPos === false || $endPos === false) {
        return $string;
    }

    $textToDelete = substr(
        $string,
        $beginningPos,
        $endPos + strlen($end) - $beginningPos
    );

    return sp_string_remove_all_between(
        $beginning,
        $end,
        str_replace($textToDelete, '', $string)
    ); // recursion to ensure all occurrences are replaced
}

function create_tracking_id()
{
    $tracking_id = strtoupper(sp_random_strings(10));
    return substr($tracking_id, 0, 4) . '-' . substr($tracking_id, 4);
}

/**
 * @param $name
 * @param $type
 * @return string
 */
function categoryCalculateTotalByName($name, $type)
{
    $t = Transaction::where('type', $type)
        ->where('category', $name)
        ->sum('amount');

    if ($t == '' || $t == '0') {
        return '0.00';
    }

    return $t;
}

/**
 * @param array $data
 * @param int $code
 */
function jsonResponse($data = [], $code = 200)
{
    http_response_code($code);
    header('Content-type: application/json');
    echo json_encode($data);
    exit();
}

function sp_display_in_the_console($message)
{
    $message = date("H:i:s") . " - $message" . PHP_EOL;
    print $message;
    flush();
}

function sp_get_string_between($string, $start, $end)
{
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) {
        return '';
    }
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}

function sp_get_last_n_days($days, $format = 'Y-m-d')
{
    $m = date("m");
    $de = date("d");
    $y = date("Y");
    $dateArray = [];
    for ($i = 0; $i <= $days - 1; $i++) {
        $dateArray[] = date($format, mktime(0, 0, 0, $m, $de - $i, $y));
    }
    return array_reverse($dateArray);
}

function sp_uuid()
{
    $data = random_bytes(16);
    assert(strlen($data) == 16);
    $data[6] = chr((ord($data[6]) & 0x0f) | 0x40); // set version to 0100
    $data[8] = chr((ord($data[8]) & 0x3f) | 0x80); // set bits 6-7 to 10
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

/**
 * @param $option
 * @return bool
 */
function delete_option($option)
{
    $config_exist = AppConfig::where('setting', $option)->first();

    if ($config_exist) {
        $config_exist->delete();
        return true;
    }

    return false;
}

/**
 * @param array $data
 */
function api_response($data = [], $code = 200)
{
    http_response_code($code);
    header('Content-type: application/json');
    echo json_encode($data);
    exit();
}

function __($string)
{
    global $_L;
    if (!empty($_L[$string])) {
        return $_L[$string];
    }
    return $string;
}

function getContactFormattedAddress($config, $contact, $html = true)
{
    $address_format = isset($config['address_format']) ? $config['address_format'] : 'default';

    $address = $contact->address;
    $city = $contact->city;
    $state = $contact->state;
    $zip = $contact->zip;
    $country = $contact->country;

    $new_line = $html ? ' <br>' : PHP_EOL;

    switch ($address_format) {
        case 'eu':
            $address_full =
                $address .
                $new_line .
                $zip .
                ' ' .
                $city .
                $new_line .
                $state .
                $new_line .
                $country;

            break;

        case 'default':
        default:
            $address_full =
                $address .
                $new_line .
                $city .
                $new_line .
                $state .
                ' ' .
                $zip .
                $new_line .
                $country;

            break;
    }

    return $address_full;
}

/**
 * @param $line
 * @param string $fallback
 * @return mixed|string
 */
function ib_lan_get_line($line, $fallback = '')
{
    global $_L;
    if (isset($_L[$line])) {
        return str_replace($line, $_L[$line], $line);
    } elseif ($fallback != '') {
        return $fallback;
    } else {
        return $line;
    }
}

/**
 * @param $number
 * @param $currency_iso_code
 * @param bool $precision
 * @return string
 */
function numberFormatUsingCurrency(
    $number,
    $currency_iso_code,
    $precision = false
) {
    if ($currency_iso_code == 'USD') {
        return $number;
    }

    $currency = Currency::getAllCurrencies();

    if (isset($currency[$currency_iso_code])) {
        $selected_currency = $currency[$currency_iso_code];

        if (!$precision) {
            $precision = $selected_currency['precision'];
        }

        $number = number_format(
            $number,
            $precision,
            $selected_currency['decimal_mark'],
            $selected_currency['thousands_separator']
        );
    }

    return $number;
}

function sp_transform_string_template($string)
{
    try {
        $smarty = new Smarty();
        $smarty->setCompileDir('storage/compiled/');
        return $smarty->fetch('string:' . $string);
    } catch (\Exception $e) {
        return $string;
    }
}

function predict_next_serial($config, $type)
{
    switch ($type) {
        case 'customer':
            return $config['customer_code_prefix'] .
                str_pad(
                    $config['customer_code_current_number'],
                    $config['number_pad'],
                    '0',
                    STR_PAD_LEFT
                );

            break;

        case 'company':
            return $config['company_code_prefix'] .
                str_pad(
                    $config['company_code_current_number'],
                    $config['number_pad'],
                    '0',
                    STR_PAD_LEFT
                );

            break;

        case 'income':
            return $config['income_code_prefix'] .
                str_pad(
                    $config['income_code_current_number'],
                    $config['number_pad'],
                    '0',
                    STR_PAD_LEFT
                );

            break;

        case 'expense':
            return $config['expense_code_prefix'] .
                str_pad(
                    $config['expense_code_current_number'],
                    $config['number_pad'],
                    '0',
                    STR_PAD_LEFT
                );

            break;

        case 'invoice':
            return sp_transform_string_template(
                $config['invoice_code_prefix']
            ) .
                str_pad(
                    $config['invoice_code_current_number'],
                    $config['number_pad'],
                    '0',
                    STR_PAD_LEFT
                );

            break;

        case 'purchase':
            return $config['purchase_code_prefix'] .
                str_pad(
                    $config['purchase_code_current_number'],
                    $config['number_pad'],
                    '0',
                    STR_PAD_LEFT
                );

            break;

        case 'quote':
            return $config['quotation_code_prefix'] .
                str_pad(
                    $config['quotation_code_current_number'],
                    $config['number_pad'],
                    '0',
                    STR_PAD_LEFT
                );

            break;

        case 'supplier':
            return $config['supplier_code_prefix'] .
                str_pad(
                    $config['supplier_code_current_number'],
                    $config['number_pad'],
                    '0',
                    STR_PAD_LEFT
                );

            break;

        case 'credit-note':
            return ($config['credit_note_code_prefix'] ?? __('CN-')) .
                str_pad(
                    ($config['credit_note_code_current_number'] ?? 1),
                    $config['number_pad'],
                    '0',
                    STR_PAD_LEFT
                );

            break;

    }
}

function sp_config_or_default($key, $default_value = '')
{
    global $config;
    if (!empty($config[$key])) {
        return $config[$key];
    }

    return $default_value;
}

function format_number_using_currency_code($number, $currency_code)
{
    $currency = Currency::getAllCurrencies();

    if (isset($currency[$currency_code])) {
        $selected_currency = $currency[$currency_code];

        $number = number_format(
            $number,
            $selected_currency['precision'],
            $selected_currency['decimal_mark'],
            $selected_currency['thousands_separator']
        );
    }

    return $number;
}

function generate_random_password($char = 8)
{
    $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890@#*';
    $pass = [];
    $alphaLength = strlen($alphabet) - 1;
    for ($i = 0; $i < $char; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }

    return implode($pass);
}

/**
 * @param $setting
 * @param $value
 * @param bool $create
 *
 * @return bool
 */
function updateOption($setting, $value, $create = false)
{
    $check = AppConfig::where('setting', $setting)->first();

    if ($check) {
        if (is_null($value)) {
            $check->delete();
        }

        $check->setting = $setting;
        $check->value = $value;
        $check->save();
        return true;
    }

    if ($create) {
        if (is_null($value)) {
            return false;
        }
        // create option
        $create = new AppConfig();
        $create->setting = $setting;
        $create->value = $value;
        $create->save();
        return true;
    }

    return false;
}

function removeOption($key)
{
    $option = AppConfig::where('setting', $key)->first();
    if ($option) {
        $option->delete();
    }
}

function get_discount_amount($discount_amount, $type, $total_amount)
{
    if ($type == 'percentage' || $type == 'p') {
        return ($discount_amount / 100) * $total_amount;
    }

    return $discount_amount;
}
