<?php
namespace Modules\Account\Controllers;

use \Entity\User;

class RecoverController extends BaseController
{
    public function indexAction()
    {
        $form = new \FA\Form($this->current_module_config->forms->recover);

        if ($_POST && $form->isValid($_POST))
        {
            $data = $form->getValues();
            $data['email'] = mb_strtolower($data['email'], 'UTF-8');

            $user = User::getRepository()->findOneBy(array('username' => $data['username'], 'email' => $data['email']));

            if ($user instanceof User)
            {
                $user->lostpw = \FA\Legacy\Utilities::uuid();
                $user->save();

                \FA\Messenger::send(array(
                    'to'        => $user->email,
                    'subject'   => 'Password Recovery Code',
                    'template'  => 'account_recover',
                    'vars'      => array(
                        'id'        => $user->id,
                        'code'      => $user->lostpw,
                    ),
                ));

                $this->alert('<b>A password recovery link has been sent to your e-mail address.</b><br>Click the link in the e-mail to reset your password.', 'green');
                return $this->redirectHome();
            }
            else
            {
                $form->addError('username', 'We could not locate an account with this username and e-mail address in our system. Please try again!');
            }
        }

        $this->view->setVar('title', 'Forgot My Password');
        return $this->renderForm($form);
    }

    public function verifyAction()
    {
        $id = (int)$this->getParam('id');
        $code = trim($this->getParam('code'));

        if ($id == 0 || empty($code))
            throw new \FA\Exception('This page requires a valid user ID and recovery code.');

        $user = User::getRepository()->findOneBy(array('id' => $id, 'lostpw' => $code));

        if (!($user instanceof User))
            throw new \FA\Exception('Invalid ID or recovery code provided!');

        // Reset the "lost password" code.
        $user->lostpw = NULL;
        $user->save();


        $this->auth->setUser($user);
    }
}