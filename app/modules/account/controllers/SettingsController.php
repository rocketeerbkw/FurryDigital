<?php
namespace Modules\Account\Controllers;

use Entity\User;
use Entity\UserContact;

class SettingsController extends BaseController
{
    public function indexAction()
    {
        // Initialize the form.
        $form = new \FA\Form($this->current_module_config->forms->settings_account->toArray());

        // Set form defaults based on current user database records.
        $form->setDefaults(array_merge(
            $this->user->toArray(FALSE, TRUE),
            array('birthday' => $this->user->getBirthday()),
            $this->user->getVariables()
        ));

        if ($_POST && $form->isValid($_POST))
        {
            $data = $form->getValues();

            // Check for new password.
            if (!empty($data['new_password']))
            {
                if (strcmp($data['new_password'], $data['new_password_confirm']))
                    $form->addError('new_password_confirm', 'The two passwords did not match.');

                if (strlen($data['new_password']) < 6)
                    $form->addError('new_password', 'Passwords must be at least 6 characters long.');

                // Add the password to the information to be changed if valid.
                $data['user']['password'] = $data['new_password'];
            }

            if (!$form->hasErrors())
            {
                // Load data directly into DB models using the fromArray helpers.
                $this->user->fromArray($data['user']);

                $this->user->setVariables($data['vars']);
                $this->em->persist($this->user);

                // Push any model changes to the DB.
                $this->em->flush();

                $this->alert('<b>Settings updated!</b><br>Your changes have been saved.', 'green');
                return $this->redirectHere();
            }
        }

        $this->view->form = $form;
    }

    public function profileAction()
    {
        $form_config = $this->current_module_config->forms->settings_profile->toArray();

        // Populate the submission selection dropdowns.
        $submissions = $this->em->createQuery('SELECT s.id, s.title, s.is_scrap FROM Entity\Upload s WHERE s.user_id = :user_id')
            ->setParameter('user_id', $this->user->id)
            ->getArrayResult();

        $submission_select = array(
            'featured' => array('' => 'Disabled'),
            'profile_pic' => array('' => 'Disabled'),
        );

        foreach($submissions as $submission)
        {
            $group = ($submission['is_scrap']) ? 'profile_pic' : 'featured';
            $submission_select[$group][$submission['id']] = $submission['title'];
        }

        $form_config['groups']['featured_items']['elements']['featured'][1]['options'] = $submission_select['featured'];
        $form_config['groups']['featured_items']['elements']['profile_pic'][1]['options'] = $submission_select['profile_pic'];

        // Initialize the form.
        $form = new \FA\Form($form_config);

        // Set form defaults based on current user database records.
        $contact = $this->user->contact;
        if (!($contact instanceof UserContact))
        {
            $contact = new UserContact;
            $contact->user = $this->user;
            $contact->save();
        }

        $form->setDefaults(array_merge(
            $this->user->toArray(FALSE, TRUE),
            $contact->toArray(FALSE, TRUE)
        ));

        if ($_POST && $form->isValid($_POST))
        {
            $data = $form->getValues();

            // Load data directly into DB models using the fromArray helpers.
            $this->user->fromArray($data['user']);
            $this->em->persist($this->user);

            $contact->fromArray($data['contact']);
            $this->em->persist($contact);

            // Push any model changes to the DB.
            $this->em->flush();

            $this->alert('<b>Profile Updated!</b><br>Your changes have been saved.', 'green');
            return $this->redirectHere();
        }

        $this->view->form = $form;
    }
}