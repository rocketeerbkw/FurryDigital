<?php
namespace Modules\Account\Controllers;

class IndexController extends BaseController
{
    public function indexAction()
    {
        // LEGACY: Do a quick check to make sure the user's avatar is valid, copy the default if not.
        $user = $this->auth->getLoggedInUser();
        $avatar_base = $this->config->application->avatars_path;
        $user_avatar = $avatar_base.'/'.$user->lower.'.gif';

        $default_avatar = FA_INCLUDE_STATIC.'/img/avatar.gif';

        if (!file_exists($user_avatar))
            @copy($default_avatar, $user_avatar);
    }
}