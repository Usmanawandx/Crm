<?php
use Illuminate\Database\Eloquent\Model;

class Widget extends Model
{
    public static function defaultWidget($key)
    {
        $widgets = [
            'client-auth-page-widget' => '<h2>Welcome</h2>
<p>Your Metro Computers account connects all your services, billing and support in one location.
Sign in to manage your account. You do not have an account yet? Create a new.</p>
<p></p><p></p><p>You may go <a href="javascript:history.go(-1)">Back To The Previous Page</a></p>',
        ];

        return $widgets[$key] ?? '';
    }

    public static function getWidgetContent($type)
    {
        $widget = self::where('type',$type)->first();
        if($widget && $widget->content)
        {
            return $widget->content;
        }
        return  self::defaultWidget($type);
    }
}
