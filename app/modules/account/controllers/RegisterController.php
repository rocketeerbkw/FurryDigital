<?php
namespace Modules\Account\Controllers;

use Entity\RegistrationRequest;
use Entity\User;

class RegisterController extends BaseController
{
    public function permissions()
    {
        return true;
    }

    public function preDispatch()
    {
        if ($this->auth->isLoggedIn())
            return $this->redirectHome();

        // Check if registration is disabled in settings.
        $fa_settings = $this->di->get('fa')->settings;
        if($fa_settings['Disable_Registration'] == 1)
            throw new \App\Exception($fa_settings['Disable_Registration_Reason']);
    }

    public function indexAction()
    {
        if (!$_POST)
            $this->forceSecure();

        $form = new \App\Form($this->current_module_config->forms->register);

        if ($_POST && $form->isValid($_POST))
        {
            $data = $form->getValues();

            // Always make the e-mails lower case.
            $data['email'] = mb_strtolower($data['email'], 'UTF-8');
            $data['email2'] = mb_strtolower($data['email2'], 'UTF-8');

            // Validate length, format, used status of the username.
            $status = $this->_checkUsername($data['username']);
            if (!$status['valid'])
                $form->addError('username', $status['message']);

            // Validate e-mail address against junk providers.
            $fa_settings = $this->di->get('fa')->settings;
            $blocked_providers = explode("\n", str_replace("\r", '', $fa_settings['Junk_Email_Providers']));

            foreach($blocked_providers as $provider)
            {
                $provider = trim(strtolower($provider));

                if(!empty($provider) && strpos(strtolower($data['email']), $provider) !== FALSE)
                {
                    $form->addError('email', 'The use of disposable e-mail addresses or e-mail forwarders has been disabled. Please use your real e-mail address.');
                    break;
                }
            }

            // Check if e-mail address uses a common "spam e-mail" pattern.
            // f4nknve3@f4nknve3.dafa88.mobi
            $spam_email_regex = '/^([a-z0-9]{8})@\1\./';
            if(preg_match($spam_email_regex, $data['email']))
                $form->addError('email', 'The use of disposable e-mail addresses or e-mail forwarders has been disabled. Please use your real e-mail address.');

            // Validation successful, send e-mail
            if (!$form->hasErrors())
            {
                $rr = new RegistrationRequest;
                $rr->username = $data['username'];
                $rr->email = $data['email'];
                $rr->save();

                $confirmation_code = $rr->confirmation_code;

                \App\Messenger::send(array(
                    'to'        => $data['email'],
                    'subject'   => 'Verify Your FloofClub Account',
                    'template'  => 'account_registration',
                    'vars'      => array(
                        'form_data'         => $data,
                        'confirmation_code' => $confirmation_code,
                    ),
                ));

                $this->view->email = $data['email'];

                return $this->view->pick('register/confirm');
            }
        }

        $this->view->title = 'Register New Account';
        return $this->renderForm($form);
    }

    public function verifyAction()
    {
        if (!$this->hasParam('code'))
            throw new \App\Exception('No verification code was provided! Your e-mail should have included a verification code.');

        $code = $this->getParam('code');
        $rr = RegistrationRequest::validate($code);

        if (!($rr instanceof RegistrationRequest))
            throw new \App\Exception('Your verification code could not be validated. The code may have expired, or already been used.');

        $form = new \App\Form($this->current_module_config->forms->register_complete);

        $form->setDefaults(array(
            'username'      => $rr->username,
            'email'         => $rr->email,
        ));

        if ($_POST && $form->isValid($_POST))
        {
            $data = $form->getValues();

            $bday_timestamp = strtotime($data['birthday'].' 00:00:00');
            $bday_threshold = strtotime('-13 years');

            // Rebuild the birthday into this format (in case it wasn't provided this way by the browser).
            $data['birthday'] = date('Y-m-d', $bday_timestamp);

            if ($bday_timestamp == 0)
                $form->addError('birthday', 'We could not process your birthday as specified. Please try again.');

            if ($bday_timestamp >= $bday_threshold)
                $form->addError('birthday', 'Our site cannot accept users under 13 years of age due to United States federal law, 15 USC 6501-6506.');

            if (!$form->hasErrors())
            {
                $user = new User;
                $user->fromArray(array(
                    'username'      => $rr->username,
                    'password'      => $data['password'],
                    'birthday'      => $data['birthday'],
                    'fullname'      => $data['fullname'],
                    'email'         => $rr->email,
                    'regemail'      => $rr->email,
                    'regbdate'      => str_replace('-', '', $data['birthday']),
                ));
                $user->save();

                $rr->is_used = true;
                $rr->save();

                // Create "skeleton" art folder.
                $app_cfg = $this->config->application;

                $user_art_dir = $app_cfg->art_path.'/'.$user->lower;
                @mkdir($user_art_dir);

                foreach($app_cfg->art_folders as $art_folder)
                {
                    $art_folder_path = $user_art_dir.'/'.$art_folder;
                    @mkdir($art_folder_path);
                }

                // Log in the user.
                $this->auth->setUser($user);

                $this->alert('<b>Welcome to FloofClub!</b><br>Your account has been created, and you are now logged in to the web site.', 'green');
                return $this->redirectHome();
                // return $this->view->pick('register/welcome');
            }
        }

        $this->view->title = 'Complete New Account Creation';
        return $this->renderForm($form);
    }

    /**
     * Validate a username against all availability requirements.
     *
     * @param $username
     * @return array [ valid: true/false, message: 'Reason why invalid' ]
     */
    protected function _checkUsername($username)
    {
        $username = trim($username);
        $lower = User::getLowerCase($username);

        // Username must exist.
        if (empty($username))
            return array('valid' => FALSE, 'message' => 'Username not specified.');

        // Username must only contain letters, numbers, -_~.
        if(!preg_match('/^([a-zA-Z0-9_.~-]+)$/', $username))
            return array('valid' => FALSE, 'message' => 'Username contains invalid characters. Only letters and numbers, dash, underscore, tilde and a period are allowed.');

        // Username must be at least 3 characters long.
        if(strlen($lower) < 3)
            return array('valid' => FALSE, 'message' => 'Username must contain at least three alphanumeric characters.');

        // Username must not start with a period.
        if ($lower[0] == '.')
            return array('valid' => FALSE, 'message' => 'Usernames must not start with a period.');

        // Username must not contain forbidden words.
        $fa_settings = $this->di->get('fa')->settings;
        $blocked_words = explode(' ', strtolower(str_replace(array("\n", "\r"), array(' ', ''), $fa_settings['Account_Name_Blocklist'])));

        $found = FALSE;
        $word = null;

        foreach($blocked_words as $word)
        {
            if(trim($word) and (strpos($username, $word) !== FALSE || strpos(strtolower($username), $word) !== FALSE))
            {
                $found = TRUE;
                break;
            }
        }

        if($found)
            return array('valid' => FALSE, 'message' => 'The word "'.$word.'" is forbidden in usernames.');

        // Check if account exists.
        $existing_user = User::getRepository()->findOneBy(array('lower' => $lower));

        if ($existing_user instanceof User)
        {
            if ($existing_user->accesslevel == User::LEGACY_ACL_BANNED)
                return array('valid' => FALSE, 'message' => 'This username already exists and is banned.');
            else
                return array('valid' => FALSE, 'message' => 'This username already exists!');
        }

        // Check if reservation exists.
        $existing_reservation = RegistrationRequest::getRepository()->findOneBy(array('lower' => $lower));

        if ($existing_reservation instanceof RegistrationRequest)
        {
            // Reservations more than 24 hours old are expired.
            if ($existing_reservation->created_at >= time()-86400)
                return array('valid' => FALSE, 'message' => 'A registration request already exists for this username. Check your e-mail for more information!');
        }

        // Return valid if none of the above checks failed!
        return array('valid' => TRUE, 'message' => '');
    }
}