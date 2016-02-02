<?php
namespace Modules\Profile\Controllers;

use Entity\Upload;

class FavoritesController extends BaseController
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
        $perpage = $this->user->getVariable('perpage');

        // Maturity Rating Filter
        if ($this->fa->canSeeArt('adult'))
            $maturity_filter = array(Upload::RATING_GENERAL, Upload::RATING_ADULT, Upload::RATING_MATURE);
        elseif ($this->fa->canSeeArt('mature'))
            $maturity_filter = array(Upload::RATING_GENERAL, Upload::RATING_MATURE);
        else
            $maturity_filter = array(Upload::RATING_GENERAL);

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

        if (!empty($fav_maturity_filter))
        {
            $query = $this->em->createQuery('SELECT f, up FROM Entity\Favorite f JOIN f.upload up WHERE f.user_id = :user_id AND up.rating IN (:ratings) ORDER BY f.id DESC')
                ->setParameter('user_id', $this->owner->id)
                ->setParameter('ratings', $fav_maturity_filter);

            $pager = new \FA\Paginator\Doctrine($query, $this->getParam('page', 1), $perpage);
            $this->view->pager = $pager;

            if (count($pager) > 0)
            {
                foreach($pager as $fav_row)
                {
                    if ($fav_row->upload->rating == Upload::RATING_ADULT)
                        $this->fa->setPageHasMatureContent();
                }
            }
        }
    }

    public function listAction()
    {
        /*
        $sid = _get('sid', FALSE, 'intval');

        if(!$sid) {
            terminate();
        }


        $q= 'SELECT user, title '.
            'FROM   submissions '.
            'WHERE  rowid='.$sql->qstr($sid);
        $submission_info = $sql->get_row($q);

        if(!$submission_info) {
            terminate();
        }

        ($submission_info['user'] == $_USER['userid'] or $_USER['accesslevel'] == 1)
        or
        terminate();



        $page = _get('page', 1, 'intval');
        $page <= 0
        and
        $page = 1;

        $perpage = 1000;


        $nextpage = $page+1;
        $lastpage = $page > 1 ? $page-1 : 1;


        $prev_listname = '^';
        $faves = array();


        $q= 'SELECT   u.username, u.lower, u.accesslevel '.
            'FROM     favorites AS f LEFT JOIN users AS u ON f.user_id=u.userid '.
            'WHERE    f.submission_id='.$sql->qstr($sid).' '.
            'ORDER BY u.username ASC '.
            'LIMIT    '.lim2($page-1, $perpage).' ';
        $result = $sql->query($q);

        $iC1 = 0;
        while($row = $sql->fetch_array($result))
        {
            $usersymbol = usersymbol($row['accesslevel']);

            $prev_lname_firstchar = strtolower($prev_listname[0]);
            $curr_lname_firstchar = strtolower($row['username'][0]);
            $curr_lname_code = ord($curr_lname_firstchar);
            $spacer = ( ($prev_lname_firstchar == $curr_lname_firstchar) or !($curr_lname_code >= 97 and $curr_lname_code <= 122))
                ?
                ''
                :
                '<br />';

            $faves[] = $spacer.$usersymbol.' '.
                '<a href="/user/'.htmlspecialchars($row['lower']).'/">'.
                '<span class="artist_name">'.htmlspecialchars($row['username']).'</span>'.
                '</a>';

            $iC1++;

            $prev_listname = $row['username'];
        }
        $sql->free_result($result);



        $iC1 == $perpage
        or
        $nextpage = $page;


        $faves = table($faves, 1, 'NULL', 'left');

        $_HEADER->template('header-min');
        $minhead = $_HEADER->render();


        $tpl     = new Template('footer-min');
        $minfoot = $tpl->render();


        $tpl = new Template('faveslist/index');
        $tpl->assign('minhead'  , $minhead);
        $tpl->assign('title'    , $submission_info['title']);
        $tpl->assign('faves'    , $faves);
        $tpl->assign('sid'      , $sid);
        $tpl->assign('lastpage' , $lastpage);
        $tpl->assign('perpage'  , $perpage);
        $tpl->assign('nextpage' , $nextpage);
        $tpl->assign('minfoot'  , $minfoot);
        $tpl->display();
        */
    }
}