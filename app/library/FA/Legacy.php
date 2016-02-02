<?php
/**
 * FloofClub Legacy Class
 *
 * This class exposes several critical functions to the global Dependency Injection (DI) container as $di->fa.
 * It is, itself, DI-aware, so it can call other DI containers as needed.
 *
 * Do not use this class as a universal "catch-all" for global functions unless absolutely necessary!!
 */

namespace FA;

use Entity\User;
use Zend\Stdlib\ArrayObject;

class Legacy
{
    const SITE_MODE_NORMAL      = 0;
    const SITE_MODE_READONLY    = 1;
    const SITE_MODE_FILE_READONLY = 6;
    const SITE_MODE_ADMIN       = 2;
    const SITE_MODE_OFFLINE     = 5;

    /**
     * @var \Entity\User The current logged in user information (or a blank placeholder array).
     */
    public $user;

    /**
     * @var \Entity\User The logged in user object (if valid).
     */
    protected $user_obj;

    /**
     * @var array FA-specific site settings loaded from global configuration.
     */
    public $settings = array();

    /**
     * @var bool Indicates whether the currently displayed page has mature content.
     */
    public $page_has_mature_content = false;

    /**
     * @var string The cookie name for the "SFW mode" cookie.
     */
    public $sfw_cookie_name = 'sfw';

    /**
     * @var bool Internal variable indicating whether the init() function below has been run.
     */
    protected $is_initialized = false;

    /**
     * @var \Phalcon\DiInterface Dependency Injection interface.
     */
    protected $di;

    public function __construct(\Phalcon\DiInterface $di = null)
    {
        $this->di = $di;
    }

    /**
     * Initialize the FA legacy class.
     * @return bool
     */
    public function init()
    {
        if ($this->is_initialized)
            return false;

        // Load the site-wide settings from the global config.
        $config = $this->di->get('config');
        $this->settings = $config->fa->system->toArray();

        // Initialize the FA-specific user array and load it into the DI container.
        $this->user = $this->di->get('user');

        // Check for special site modes.
        $flash = $this->di->get('flash');

        switch($this->settings['System_Mode'])
        {
            case self::SITE_MODE_ADMIN:
                // TODO: Implement "Admin Mode" using new ACL.
                break;

            case self::SITE_MODE_READONLY:
                $flash->addMessage('FloofClub is in read-only mode.', 'warning');
                break;

            case self::SITE_MODE_FILE_READONLY:
                $flash->addMessage('FloofClub is in file read-only mode.', 'warning');
                break;

            case self::SITE_MODE_OFFLINE:
                die('FloofClub is currently offline. Thank you for your patience!');
                break;
        }

        // Show administrator message from config.
        if (!empty($this->settings['Admin_Message']))
        {
            $flash->addMessage($this->settings['Admin_Message'], 'info');
        }

        // Show account disabled message if applicable.
        $vars = $this->user->getVariables();

        if ($vars['account_disabled'])
        {
            $flash->addMessage('This account is currently <b>disabled</b>. Visit "Account Settings" to modify this.', 'error');
        }

        /*
        $news_block = '';

        $news_cookie = _cookie(NEWS_COOKIE_NAME, false, 'intval');

        // fetch fender's last journal
        $q= 'SELECT   row_id, date_posted, subject '.
        'FROM     journals '.
        'WHERE    user_id=8 '.
        'ORDER BY row_id DESC '.
        'LIMIT    1';
        $news_data = $sql->get_row($q, 'frontpage, fetch news', __FILE__);

        if($news_data && (!$news_cookie || $news_cookie < $news_data['date_posted'])) {
        // render news post
        $tpl = new Template('news_block');

        $news_data['subject'] = htmlspecialchars($news_data['subject']);
        $news_data['subject'] = parse_bbcode($news_data['subject']);

        $tpl->assign('journal_id'  , $news_data['row_id']);
        $tpl->assign('subject'     , $news_data['subject']);
        $tpl->assign('date_posted' , $news_data['date_posted']);

        $news_block .= $tpl->render();
        }
        */

        $this->is_initialized = true;
        return true;
    }

    /**
     * Global permissions check for whether a user can see art of a certain adult rating.
     *
     * @param $art_type
     * @return bool
     */
    public function canSeeArt($art_type, $include_cookie = true)
    {
        $art_type = strtolower($art_type);

        // Any art type besides "mature" and "adult" can always be seen.
        if ($art_type != 'mature' && $art_type != 'adult')
            return true;

        // Check for the X-FA-Force-Worksafe header.
        if (isset($_SERVER['X-FA-Force-Worksafe']) && $_SERVER['X-FA-Force-Worksafe'] == true)
            return false;

        // Check for the SFW cookie if necessary.
        if ($include_cookie && $this->getSfwCookie())
            return false;

        // Anonymous users have no additional access.
        $auth = $this->di->get('auth');
        if (!$auth->isLoggedIn())
            return false;

        $can_see_mature_art = false;
        $can_see_adult_art  = false;

        // Check the user's birthday.
        $birthday  = mktime(0, 0, 0, intval($this->user['bdaymonth']), intval($this->user['bdayday']), intval($this->user['bdayyear']));
        $cutyear   = mktime(0, 0, 0, date('m'), date('d'), date('Y')-18);

        if ($birthday >= $cutyear || $this->user['maturelocked'])
            return false;

        /**
         * user.seeadultart has 3 values
         *   0: only general
         *   1: general, mature and adult
         *   2: general and mature
         */

        if ($this->user['seeadultart'] >= 1)
            $can_see_mature_art = true;

        if ($this->user['seeadultart'] == 1)
            $can_see_adult_art = true;

        if ($art_type == 'mature')
            return $can_see_mature_art;
        elseif ($art_type == 'adult')
            return $can_see_adult_art;
        else
            return false;
    }

    public function getSfwCookie()
    {
        if (!empty($_COOKIE[$this->sfw_cookie_name]))
            return ($_COOKIE[$this->sfw_cookie_name] == 1);
        else
            return false;
    }

    /**
     * Set a new value for the "Page Has Mature Content" flag.
     *
     * @param bool|TRUE $new_value
     */
    public function setPageHasMatureContent($new_value = TRUE)
    {
        $this->page_has_mature_content = $new_value;
    }

    /**
     * Prevent a page from executing that should not execute during read-only mode.
     *
     * @param bool|FALSE $force
     * @throws \FA\Exception
     */
    public function readOnly($force = false)
    {
        if($this->settings['System_Mode'] == self::SITE_MODE_READONLY || $force)
            throw new Exception('The site is currently in read-only mode. As a result, this function is restricted. Please try again later.');
    }

    /**
     * Prevent a page from rendering that should not execute during file read-only mode.
     *
     * @param bool|FALSE $force
     * @throws \FA\Exception
     */
    public function fileReadOnly($force = FALSE)
    {
        if($this->settings['System_Mode'] == self::SITE_MODE_FILE_READONLY || $force)
            throw new Exception('The site is currently in file read-only mode. As a result, this function is restricted. Please try again later.');
    }

    /**
     * Format a date according to a user's "fuzzy dates" preference.
     *
     * @param $timestamp
     * @return string
     */
    public function formatDate($timestamp)
    {
        $date_full = date($this->settings['Long_Date_Format'], $timestamp);
        $date_fuzzy = Legacy\Utilities::age($timestamp, false, false, false, ' ago', 'raw_output');

        if ($this->user->getVariable('date_format') == 'full')
            return '<span title="' . $date_fuzzy . '" class="popup_date">' . $date_full . '</span>';
        else
            return '<span title="' . $date_full . '" class="popup_date">' . $date_fuzzy . '</span>';
    }
}