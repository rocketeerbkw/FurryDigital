<?php
namespace Modules\Profile\Controllers;

use Entity\Shout;
use Entity\Upload;
use Entity\User;
use Entity\Watch;

class IndexController extends BaseController
{
    public function preDispatch()
    {
        $this->_getUser();
        $this->_logPageView();
        $this->_enforceNoGuests();

        parent::preDispatch();
    }

    public function indexAction()
    {
        // Compile all social info.
        $owner_social = array();
        $social_types = $this->config->fa->social->toArray();
        $escaper = new \Phalcon\Escaper();

        foreach($social_types as $social_category => $social_items)
        {
            foreach($social_items as $social_type => $social_info)
            {
                $owner_social_item = $this->owner->contact->{$social_type};
                if (empty($owner_social_item))
                    continue;

                $social_image = $this->url->getStatic('img/contact/'.$social_type.'.gif');
                $social_title = $social_info['name'].': '.$escaper->escapeHtmlAttr($owner_social_item);

                if ($social_info['format'])
                {
                    $social_url = sprintf($social_info['format'], $escaper->escapeUrl($owner_social_item));

                    $owner_social[] = '<a href="'.$social_url.'" target="_blank"><img class="contacticon" src="'.$social_image.'" title="'.$social_title.'"></a>';
                }
                else
                {
                    $owner_social[] = '<img class="contacticon" src="'.$social_image.'" title="'.$social_title.'">';
                }
            }
        }

        $this->view->owner_social = $owner_social;

        // Commission information
        $has_commissions = ($this->owner->commission_types->count() == 0);
        $this->view->has_commissions = $has_commissions;

        $this->view->accept_trades = $this->owner->getVariable('accept_trades');
        $this->view->accept_commissions = $this->owner->getVariable('accept_commissions');

        // Maturity Rating Filter
        if ($this->fa->canSeeArt('adult'))
            $maturity_filter = array(Upload::RATING_GENERAL, Upload::RATING_ADULT, Upload::RATING_MATURE);
        elseif ($this->fa->canSeeArt('mature'))
            $maturity_filter = array(Upload::RATING_GENERAL, Upload::RATING_MATURE);
        else
            $maturity_filter = array(Upload::RATING_GENERAL);

        // Profile picture.
        if ($this->owner->profile_pic)
        {
            $profile_pic = Upload::find($this->owner->profile_pic);

            if ($profile_pic instanceof Upload && in_array($profile_pic->rating, $maturity_filter))
            {
                if ($profile_pic->rating == Upload::RATING_ADULT)
                    $this->fa->setPageHasMatureContent();

                $this->view->profile_pic = $profile_pic;
            }
        }

        // Featured picture
        if ($this->owner->featured)
        {
            $featured_pic = Upload::find($this->owner->featured);

            if ($featured_pic instanceof Upload && in_array($featured_pic->rating, $maturity_filter))
            {
                if ($featured_pic->rating == Upload::RATING_ADULT)
                    $this->fa->setPageHasMatureContent();

                $this->view->featured_pic = $featured_pic;
            }
        }

        // Upload data
        $uploads = $this->em->createQuery('SELECT up FROM Entity\Upload up WHERE up.is_scrap = 0 AND up.rating IN (:ratings) AND up.user_id = :user_id ORDER BY up.id DESC')
            ->setParameter('ratings', $maturity_filter)
            ->setParameter('user_id', $this->owner->id)
            ->setMaxResults(14)
            ->execute();

        if ($uploads)
        {
            foreach($uploads as $row)
            {
                if ($row->rating == Upload::RATING_ADULT)
                    $this->fa->setPageHasMatureContent();
            }

            $this->view->latest_uploads = $uploads;
        }

        // Favorite filters
        $fav_maturity_filter = $maturity_filter;

        if ($this->acl->isAllowed('administer all') || $this->user->id == $this->owner->id)
            $fav_filter = 'n';
        else
            $fav_filter = $this->owner->getVariable('hide_favorites');

        switch($fav_filter)
        {
            case 'e': // hide everything
                $fav_maturity_filter = null;
            break;

            case 'ma': // hide adult+mature
                unset($fav_maturity_filter[Upload::RATING_MATURE], $fav_maturity_filter[Upload::RATING_ADULT]);
            break;

            case 'a': // hide adult
                unset($fav_maturity_filter[Upload::RATING_ADULT]);
            break;

            case 'n': // hide nothing
            default :
                // No changes.
            break;
        }

        // Favorites
        if (!empty($fav_maturity_filter))
        {
            $latest_faves = $this->em->createQuery('SELECT f, up FROM Entity\Favorite f JOIN f.upload up WHERE f.user_id = :user_id AND up.rating IN (:ratings) ORDER BY f.id DESC')
                ->setParameter('user_id', $this->owner->id)
                ->setParameter('ratings', $fav_maturity_filter)
                ->setMaxResults(14)
                ->execute();

            if ($latest_faves)
            {
                foreach($latest_faves as $row)
                {
                    if ($row->rating == Upload::RATING_ADULT)
                        $this->fa->setPageHasMatureContent();
                }

                $this->view->latest_faves = $latest_faves;
            }
        }

        // Watched by / Is watching counts
        $watched_by_count = $this->em->createQuery('SELECT COUNT(w.id) FROM Entity\Watch w WHERE w.target_id = :user_id')
            ->setParameter('user_id', $this->owner->id)
            ->getSingleScalarResult();

        $this->view->num_watched_by = $watched_by_count;

        $watching_count = $this->em->createQuery('SELECT COUNT(w.id) FROM Entity\Watch w WHERE w.user_id = :user_id')
            ->setParameter('user_id', $this->owner->id)
            ->getSingleScalarResult();

        $this->view->num_watching = $watching_count;

        // Most recent journal
        $journal = $this->em->createQuery('SELECT j FROM Entity\Journal j WHERE j.user_id = :user_id ORDER BY j.id DESC')
            ->setParameter('user_id', $this->owner->id)
            ->setMaxResults(1)
            ->getOneOrNullResult();

        $this->view->journal = $journal;

        // Shouts
        $shouts = $this->em->createQuery('SELECT s, us FROM Entity\Shout s JOIN s.sender us WHERE s.recipient_id = :user_id ORDER BY s.id DESC')
            ->setParameter('user_id', $this->owner->id)
            ->setMaxResults(12)
            ->execute();

        $this->view->shouts = $shouts;

        // New shout form.
        $shout_form_config = $this->current_module_config->forms->shout->toArray();
        $shout_form_config['action'] = $this->url->routeFromHere(array('action' => 'shout'));

        $shout_form = new \FA\Form($shout_form_config);
        $this->view->shout_form = $shout_form;
    }

    public function shoutAction()
    {
        $this->fa->readOnly();

        $this->acl->checkPermission('is logged in');

        if ($this->user->getVariable('account_disabled'))
        {
            $this->alert('<b>Your account is currently disabled.</b><br>You cannot make posts while your account is disabled.');
            return $this->redirectToReferrer();
        }

        // TODO: Comment rate limit
        // enforce_comment_rate_limit($PAGE_OWNER_INFO['userid']);

        // Check blocklist
        if ($this->owner->isBlocked($this->user))
        {
            $this->alert('<b>Could not post shout.</b><br>Error: User has blocked you.', 'red');
            return $this->redirectToReferrer();
        }

        // Check for disabled shouts.
        if ($this->owner->getVariable('disable_shouts') == 1)
        {
            $this->alert('<b>Shouts have been disabled on this user\'s account.</b><br>Your shout could not be posted.', 'red');
            return $this->redirectToReferrer();
        }

        $form_config = $this->current_module_config->forms->shout->toArray();
        $form = new \FA\Form($form_config);

        if ($this->request->isPost() && $form->isValid($_POST))
        {
            $data = $form->getValues();

            $record = new Shout;
            $record->sender = $this->user;
            $record->recipient = $this->owner;
            $record->message = $data['shout'];

            $record->save();

            $this->alert('<b>Shout posted!</b>', 'green');
            return $this->redirectFromHere(array('name' => 'user_view'));
        }

        return $this->renderForm($form, 'edit', 'Submit a Shout');
    }

    public function watchAction()
    {
        $this->fa->readOnly();

        if ($this->user->getVariable('account_disabled'))
        {
            $this->alert('<b>Your account is currently disabled.</b><br>You cannot watch anybody while your account is disabled.', 'red');
            return $this->redirectToReferrer();
        }

        if ($this->owner->getAccessLevel() == User::LEGACY_ACL_BANNED || $this->owner->getAccessLevel() == User::LEGACY_ACL_DECEASED)
        {
            $this->alert('<b>You are not allowed to follow this user due to their current status.</b>', 'red');
            return $this->redirectToReferrer();
        }

        if ($this->owner->isBlocked($this->user))
        {
            $this->alert('<b>You cannot watch an account that has blocked you.</b>', 'red');
            return $this->redirectToReferrer();
        }

        $record = new Watch;
        $record->user = $this->user;
        $record->target = $this->owner;
        $record->save();

        $this->alert('<b>You are now watching this user!</b>', 'green');
        return $this->redirectFromHere(array('name' => 'user_view'));
    }

    public function unwatchAction()
    {
        $this->fa->readOnly();

        $watches = Watch::getRepository()->findBy(array('user_id' => $this->user->id, 'target_id' => $this->owner->id));

        if ($watches)
        {
            foreach($watches as $watch)
                $watch->delete();
        }

        $this->alert('<b>You are no longer watching this user.</b>', 'green');
        return $this->redirectFromHere(array('name' => 'user_view'));
    }
}