<?php

add_sub_menu_admin(
    'settings',
    'Two-Factor Authentication',
    U . 'twofactor/setup/init/'
);

Event::bind('admin/login/_verified', function () {
    $arg_list = func_get_args();

    $id = $arg_list[0][0];

    $user = db_find_one('sys_users', $id);

    if ($user->otp == 'Yes') {
        $_SESSION['tuid'] = $user->id;
        api_response([
            'success' => true,
            'redirect_url' => U . 'twofactor/otp/',
        ]);
    }
});
