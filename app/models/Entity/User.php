<?php
namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Users
 *
 * @Table(name="users", indexes={
 *   @Index(name="username", columns={"username"}),
 *   @Index(name="lower_index", columns={"lower"}),
 *   @Index(name="lostpw", columns={"lostpw"}),
 *   @Index(name="for_admin_tools", columns={"ip"}),
 *   @Index(name="for_admin_tools2", columns={"useremail"}),
 *   @Index(name="for_admin_tools3", columns={"regemail"})
 * })
 * @Entity
 */
class User extends \FA\Doctrine\Entity
{
    const LEGACY_ACL_REGULAR = 0;
    const LEGACY_ACL_ADMINISTRATOR = 1;
    const LEGACY_ACL_TESTER = 2;
    const LEGACY_ACL_DEVELOPER = 3;
    const LEGACY_ACL_BANNED = 4;
    const LEGACY_ACL_DECEASED = 5;

    public function __construct()
    {
        // Set default values.
        $this->seeadultart = Upload::RATING_GENERAL;
        $this->regdate = time();

        // Lazy-initialize oneToMany relationships with other tables.
        $this->admin_actions = new ArrayCollection;
        $this->commission_types = new ArrayCollection;
        $this->favorites = new ArrayCollection;
        $this->favorite_notifications = new ArrayCollection;
        $this->folders = new ArrayCollection;
        $this->folder_groups = new ArrayCollection;
        $this->journals = new ArrayCollection;
        $this->journal_comments = new ArrayCollection;
        $this->journal_comment_notifications = new ArrayCollection;
        $this->journal_notifications = new ArrayCollection;
        $this->notes_sent = new ArrayCollection;
        $this->notes_received = new ArrayCollection;
        $this->shouts_sent = new ArrayCollection;
        $this->shouts_received = new ArrayCollection;
        $this->shout_notifications = new ArrayCollection;
        $this->uploads = new ArrayCollection;
        $this->upload_comments = new ArrayCollection;
        $this->upload_comment_notifications = new ArrayCollection;
        $this->upload_notifications = new ArrayCollection;
        $this->trouble_tickets = new ArrayCollection;
        $this->trouble_tickets_assigned = new ArrayCollection;
        $this->trouble_ticket_comments = new ArrayCollection;
        $this->trouble_ticket_notifications = new ArrayCollection;
        $this->user_comments_sent = new ArrayCollection;
        $this->user_comments_received = new ArrayCollection;
        $this->suspensions = new ArrayCollection;
        $this->suspensions_enforced = new ArrayCollection;
        $this->vars = new ArrayCollection;
        $this->watches = new ArrayCollection;
        $this->watch_notifications = new ArrayCollection;
    }

    /**
     * @var integer
     * @Column(name="userid", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string
     * @Column(name="username", type="string", options={"default"=""}, length=30, nullable=false)
     */
    protected $username;

    public function setUsername($username)
    {
        $this->username = $username;
        $this->lower = self::getLowerCase($username);
    }

    /**
     * @var string
     * @Column(name="lower", type="string", options={"default"=""}, length=30, nullable=false)
     */
    protected $lower;

    /**
     * @var string
     * @Column(name="fullname", type="string", options={"default"=""}, length=200, nullable=false)
     */
    protected $fullname;

    /**
     * TODO: Unused Field (For Removal)
     * @var string
     * @Column(name="salt", type="string", options={"default"=""}, length=32)
     */
    protected $auth_password_legacy_salt;

    /**
     * @var string
     * @Column(name="userpassword", type="string", options={"default"=""}, length=50, nullable=false)
     */
    protected $auth_password_legacy_hash;

    /**
     * @var string
     * @Column(name="password_hash", type="string", options={"default"=""}, length=255, nullable=false)
     */
    protected $auth_password_new;

    /**
     * Set a new password for the account.
     *
     * @param $password
     * @return $this
     */
    public function setPassword($password)
    {
        if (trim($password))
        {
            // Set legacy password for older style authentication.
            // TODO: Remove this when the modern application becomes the sole user of the database.
            $hashed_password = crypt($password, '$2a$07$'.sha1('d67c5cbf5b01c9f91932e3b8def5e5f8').'$');
            $hashed_password = sha1($hashed_password);
            $this->auth_password_legacy_hash = $hashed_password;
            $this->auth_password_legacy_salt = 'UNUSED';

            // Set new password using PHP internal security functions.
            $this->auth_password_new = password_hash($password, \PASSWORD_DEFAULT);
        }

        return $this;
    }

    /**
     * Empty function used to override "getter" when showing profile.
     * @return string
     */
    public function getPassword()
    {
        return '';
    }

    /**
     * @var string Password Recovery Code for Forgot Password
     * @Column(name="lostpw", type="string", options={"default"=""}, length=40, nullable=true)
     */
    protected $lostpw;

    /**
     * TODO: Unused Field
     * @var string UUID
     * @Column(name="avatar_uuid", type="binary_uuid", nullable=true)
     */
    protected $_unused_avatar_uuid;

    /**
     * @var integer The last-modified time of the user's avatar.
     * @Column(name="avatar_mtime", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     */
    protected $avatar_mtime = 0;

    /**
     * Return the URL for this user's avatar, or the system default.
     *
     * @param bool|FALSE $force_show Force the avatar to be shown even if
     *                   settings prohibit it. (control panel, etc)
     * @return string The user or default avatar full URL.
     */
    public function getAvatar($force_show = false)
    {
        return self::getUserAvatar($this->lower, $this->avatar_mtime, $force_show = false);
    }

    /**
     * Replace an existing avatar with a new one.
     * @param $new_avatar_source
     */
    public function setAvatar($new_avatar_source)
    {
        $di = \Phalcon\Di::getDefault();
        $avatar_dir = $di['config']->application->avatars_path;

        /*
        // Delete an existing avatar, if one exists.
        if ($this->avatar_mtime != 0)
        {
            $old_avatar = $avatar_dir.'/'.$this->avatar_mtime.'/'.$this->lower.'.gif';

            if (file_exists($old_avatar))
                @unlink($old_avatar);
        }
        */

        // Move the specified avatar to the public avatar directory.
        $new_mtime = time();

        // $new_avatar_dest = $avatar_dir.'/'.$new_mtime.'/'.$this->lower.'.gif';
        $new_avatar_dest = $avatar_dir.'/'.$this->lower.'.gif';

        // @mkdir($avatar_dir.'/'.$new_mtime);
        @copy($new_avatar_source, $new_avatar_dest);

        $this->avatar_mtime = $new_mtime;
        $this->save();
    }

    /**
     * @var string The user's current e-mail address.
     * @Column(name="useremail", type="string", options={"default"=""}, length=150, nullable=false)
     */
    protected $email;

    /**
     * @var string The e-mail address that the user registered with.
     * @Column(name="regemail", type="string", options={"default"=""}, length=150, nullable=false)
     */
    protected $regemail;

    /**
     * @var string A string field containing a UNIX timestamp. For...some reason.
     * @Column(name="regdate", type="string", options={"default"=""}, length=35, nullable=false)
     */
    protected $regdate;

    /**
     * @var string
     * @Column(name="biography", type="string", options={"default"=""}, length=255, nullable=false)
     */
    protected $biography = '';

    /**
     * @var string
     * @Column(name="location", type="string", options={"default"=""}, length=255, nullable=false)
     */
    protected $location = '';

    /**
     * @var string
     * @Column(name="interests", type="string", options={"default"=""}, length=255, nullable=false)
     */
    protected $interests = '';

    /**
     * @var string
     * @Column(name="occupation", type="string", options={"default"=""}, length=255, nullable=false)
     */
    protected $occupation = '';

    /**
     * Set the birthday using a UNIX timestamp or YYYY-MM-DD format string.
     * @param $bday
     */
    public function setBirthday($bday)
    {
        // Accept format YYYY-MM-DD (from HTML5 date fields).
        if (!is_int($bday))
            $bday = strtotime($bday.' 00:00:00');

        $this->bdaymonth = date('m', $bday);
        $this->bdayday = date('d', $bday);
        $this->bdayyear = date('Y', $bday);

        // Calculate age.
        $date_str = date('Y-m-d', $bday);
        $tz  = new \DateTimeZone("UTC");
        $age = \DateTime::createFromFormat('Y-m-d', $date_str, $tz)->diff(new \DateTime('now', $tz))->y;

        $this->age = $age;
    }

    /**
     * Return the user's birthday as YYYY-MM-DD format or UNIX timestamp.
     *
     * @param bool|false $as_timestamp
     * @return int|string
     */
    public function getBirthday($as_timestamp = false)
    {
        $time_str = $this->bdayyear.'-'.$this->bdaymonth.'-'.$this->bdayday;

        if ($as_timestamp)
            return strtotime($time_str.' 00:00:00');
        else
            return $time_str;
    }

    /**
     * @var string
     * @Column(name="bdaymonth", type="string", options={"default"=""}, length=100, nullable=false)
     */
    protected $bdaymonth;

    /**
     * @var string
     * @Column(name="bdayday", type="string", options={"default"=""}, length=100, nullable=false)
     */
    protected $bdayday;

    /**
     * @var string
     * @Column(name="bdayyear", type="string", options={"default"=""}, length=100, nullable=false)
     */
    protected $bdayyear;

    /**
     * @var string
     * @Column(name="regbdate", type="string", options={"default"=""}, length=8, nullable=false)
     */
    protected $regbdate;

    /**
     * @var boolean
     * @Column(name="gender", type="smallint", nullable=false)
     */
    protected $gender = 0;

    /**
     * @var string
     * @Column(name="typeartist", type="string", options={"default"=""}, length=150, nullable=false)
     */
    protected $typeartist = 'Watcher';

    /**
     * @var integer Number of page views.
     * @Column(name="pageviews", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     */
    protected $pageviews = 0;

    /**
     * @var string
     * @Column(name="mood", type="string", options={"default"=""}, length=75, nullable=false)
     */
    protected $mood = '';

    /**
     * @var integer Number of uploads the user has submitted.
     * @Column(name="submissions", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     */
    protected $num_uploads = 0;

    /**
     * @var integer Number of comments the user has sent.
     * @Column(name="commentsgiven", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     */
    protected $num_comments_sent = 0;

    /**
     * @var integer Number of comments the user has received.
     * @Column(name="commentsrecieved", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     */
    protected $num_comments_received = 0;

    /**
     * @var integer Number of items the user has favorited.
     * @Column(name="favorites", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     */
    protected $num_favorites = 0;

    /**
     * @var integer Number of journals the user has submitted.
     * @Column(name="journals", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     */
    protected $num_journals = 0;

    /**
     * TODO: Unused Field
     * @var integer
     * @Column(name="submissionscount", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     */
    protected $_unused_uploads_count = 0;

    /**
     * TODO: Unused Field
     * @var integer
     * @Column(name="amessagecount", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     */
    protected $_unused_amessagecount = 0;

    /**
     * Update all of the "num_xxx" fields to be fully accurate.
     */
    public function updateCounts()
    {
        $this->num_uploads = $this->uploads->count();
        $this->num_comments_sent = $this->user_comments_sent->count();
        $this->num_comments_received = $this->user_comments_received->count();
        $this->num_favorites = $this->favorites->count();
        $this->num_journals = $this->journals->count();
    }

    /**
     * TODO: Unused Field
     * @var string
     * @Column(name="ip", type="string", options={"default"=""}, length=20, nullable=true)
     */
    protected $_unused_ip;

    /**
     * @var integer
     * @Column(name="messagescount", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     */
    protected $notify_messages = 0;

    /**
     * @var integer
     * @Column(name="commentcount", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     */
    protected $notify_comments = 0;

    /**
     * @var integer
     * @Column(name="journalcount", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     */
    protected $notify_journals = 0;

    /**
     * @var integer
     * @Column(name="submissioncount", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     */
    protected $notify_uploads = 0;

    /**
     * @var integer
     * @Column(name="favoritescount", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     */
    protected $notify_favorites = 0;

    /**
     * @var integer
     * @Column(name="notescount", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     */
    protected $notify_notes = 0;

    /**
     * @var integer
     * @Column(name="watchcount", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     */
    protected $notify_watches = 0;

    /**
     * @var integer
     * @Column(name="ttcount", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     */
    protected $notify_tickets = 0;

    /**
     * @var integer
     * @Column(name="shouts", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     */
    protected $notify_shouts = 0;

    /**
     * Fetch all "unread" notifications.
     *
     * @return array All notifications by type.
     */
    public function getNotifications()
    {
        static $notifications;

        if (!$notifications)
        {
            $notifications = array();
            $notify_types = self::getNotificationTypes();

            foreach($notify_types as $notify_key => $notify_info)
            {
                $notify_info['count'] = (int)$this->$notify_key;
                $notify_info['show'] = ($notify_info['count'] > 0);

                $notify_info['text'] = $notify_info['count'].' '.$notify_info['abbr'];

                if ($notify_info['count'] != 1)
                    $notify_info['title'] = \Doctrine\Common\Inflector\Inflector::pluralize($notify_info['title']);

                $notifications[$notify_info['short']] = $notify_info;
            }
        }

        return $notifications;
    }

    public function hasNotifications()
    {
        $notifications = $this->getNotifications();
        foreach($notifications as $row)
        {
            if ($row['show'])
                return true;
        }

        return false;
    }

    /**
     * Decrement the notification type specified by one.
     *
     * @param $notify_type
     */
    public function removeNotification($notify_type)
    {
        $var_name = 'notify_'.$notify_type;

        if ($this->$var_name != 0)
            $this->$var_name = $this->$var_name - 1;
    }

    /**
     * Increment the notification type specified by one.
     *
     * @param $notify_type
     */
    public function addNotification($notify_type)
    {
        $var_name = 'notify_'.$notify_type;
        $this->$var_name = $this->$var_name + 1;
    }

    /**
     * @var integer
     * @Column(name="featured", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     */
    protected $featured = 0;

    /**
     * @var integer
     * @Column(name="profile_pic", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     */
    protected $profile_pic = 0;

    /**
     * @var string
     * @Column(name="shell", type="string", options={"default"=""}, length=150, nullable=false)
     */
    protected $shell = '';

    /**
     * @var string
     * @Column(name="os", type="string", options={"default"=""}, length=150, nullable=false)
     */
    protected $os = '';

    /**
     * @var string
     * @Column(name="quote", type="string", options={"default"=""}, length=150, nullable=false)
     */
    protected $quote = '';

    /**
     * @var string
     * @Column(name="music", type="string", options={"default"=""}, length=150, nullable=false)
     */
    protected $music = '';

    /**
     * @var string
     * @Column(name="favoritemovie", type="string", options={"default"=""}, length=150, nullable=false)
     */
    protected $favoritemovie = '';

    /**
     * @var string
     * @Column(name="favoritegame", type="string", options={"default"=""}, length=150, nullable=false)
     */
    protected $favoritegame = '';

    /**
     * @var string
     * @Column(name="favoriteplatform", type="string", options={"default"=""}, length=150, nullable=false)
     */
    protected $favoriteplatform = '';

    /**
     * @var string
     * @Column(name="favoritemusicplayer", type="string", options={"default"=""}, length=150, nullable=false)
     */
    protected $favoritemusicplayer = '';

    /**
     * @var string
     * @Column(name="favoriteartist", type="string", options={"default"=""}, length=150, nullable=false)
     */
    protected $favoriteartist = '';

    /**
     * @var string
     * @Column(name="favoriteanimal", type="string", options={"default"=""}, length=150, nullable=false)
     */
    protected $favoriteanimal = '';

    /**
     * @var string
     * @Column(name="favoritewebsite", type="string", options={"default"=""}, length=150, nullable=false)
     */
    protected $favoritewebsite = '';

    /**
     * @var string
     * @Column(name="favoritefood", type="string", options={"default"=""}, length=150, nullable=false)
     */
    protected $favoritefood = '';

    /**
     * @var string
     * @Column(name="species", type="string", options={"default"=""}, length=150, nullable=false)
     */
    protected $species = '';

    public function getSpeciesName()
    {
        $di = \Phalcon\Di::getDefault();
        $config = $di['config'];

        $species_raw = $config->fa->species->toArray();
        $species = \FA\Legacy\Utilities::reverseArray($species_raw);

        if (isset($species[$this->species]))
            return $species[$this->species];
        else
            return NULL;
    }

    /**
     * @var string
     * @Column(name="age", type="string", options={"default"=""}, length=3, nullable=false)
     */
    protected $age = '0';

    /**
     * @var boolean
     * @Column(name="seeadultart", type="smallint", nullable=false)
     */
    protected $seeadultart;

    /**
     * @var boolean
     * @Column(name="maturelocked", type="boolean", nullable=false)
     */
    protected $maturelocked = false;

    /**
     * @var boolean
     * @Column(name="fullview", type="boolean", nullable=false)
     */
    protected $fullview = false;

    /**
     * TODO: Unused Field
     * @var string
     * @Column(name="stylefolder", type="string", options={"default"=""}, length=150, nullable=true)
     */
    protected $_unused_stylefolder;

    /**
     * TODO: Unused Field
     * @var string
     * @Column(name="stylesheet", type="string", options={"default"=""}, length=255, nullable=true)
     */
    protected $_unused_stylesheet;

    /**
     * @var string Profile summary text.
     * @Column(name="profileinfo", type="text", length=65535, nullable=false)
     */
    protected $profileinfo = '';

    /**
     * @var string
     * @Column(name="blocklist", type="text", length=65535, nullable=false)
     */
    protected $blocklist = '';

    public function isBlocked(User $user)
    {
        // Get a clean array of blocked users.
        $blocklist = array_filter(array_map('trim', explode("\n", $this->blocklist)));

        return in_array($user->lower, $blocklist);
    }

    /**
     * @var boolean
     * @Column(name="accesslevel", type="smallint", nullable=false)
     */
    protected $access_level = self::LEGACY_ACL_REGULAR;

    public function getAccessLevel()
    {
        if ($this->suspended == 1)
            return self::LEGACY_ACL_BANNED;
        else
            return $this->access_level;
    }

    public function getSymbol()
    {
        return self::getUserSymbol($this->getAccessLevel());
    }

    public function getStatus()
    {
        return self::getUserStatus($this->getAccessLevel());
    }

    /**
     * @var boolean
     * @Column(name="donator_level", type="smallint", nullable=false)
     */
    protected $donator_level = 0;

    /**
     * @var string
     * @Column(name="journalheader", type="text", length=65535, nullable=false)
     */
    protected $journalheader = '';

    /**
     * @var string
     * @Column(name="journalfooter", type="text", length=65535, nullable=false)
     */
    protected $journalfooter = '';

    /**
     * @var integer
     * @Column(name="last_tmp_submission", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     */
    protected $last_tmp_upload = 0;

    /**
     * @var integer
     * @Column(name="suspended", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     */
    protected $suspended = 0;

    /**
     * @var string
     * @Column(name="timezone", type="string", options={"default"=""}, length=5, nullable=false)
     */
    protected $timezone = '+0000'; // UTC

    /**
     * @var boolean
     * @Column(name="timezone_dst", type="boolean", nullable=false)
     */
    protected $_unused_timezone_dst = true;
    
    /**
     * TODO: Unused Field
     * Returns the timezone offset (From UTC) in hours
     *
     * @return float
     */
    public function getTimezoneDiff()
    {    
        $timezone_obj = \DateTime::createFromFormat('O', substr_replace($this->timezone, ':', 3, 0))->getTimezone();
        $current_time = time();
        
        // Determine if the timezone does DST. If so, adjust accordingly!
        $dst = (count($timezone_obj->getTransitions($current_time)) > 0 ? date("I", $current_time) : 0);
        
        // Timezone returns -XX0. Dividing it by 100 will return the offset properly. (ie EST = -5.0)
        return ($this->timezone / 100) + $dst;
    }

    /**
     * Data Relations
     */

    /**
     * @OneToMany(targetEntity="AuditAdminAction", mappedBy="user")
     */
    protected $admin_actions;

    /**
     * @OneToMany(targetEntity="CommissionType", mappedBy="user")
     */
    protected $commission_types;

    /**
     * @OneToMany(targetEntity="Favorite", mappedBy="user", fetch="EXTRA_LAZY")
     */
    protected $favorites;

    /**
     * @OneToMany(targetEntity="FavoriteNotify", mappedBy="user")
     */
    protected $favorite_notifications;

    /**
     * @OneToMany(targetEntity="Folder", mappedBy="user")
     */
    protected $folders;

    /**
     * @OneToMany(targetEntity="FolderGroup", mappedBy="user")
     */
    protected $folder_groups;

    /**
     * @OneToMany(targetEntity="Journal", mappedBy="user", fetch="EXTRA_LAZY")
     */
    protected $journals;

    /**
     * @OneToMany(targetEntity="JournalComment", mappedBy="user")
     */
    protected $journal_comments;

    /**
     * @OneToMany(targetEntity="JournalCommentNotify", mappedBy="user")
     */
    protected $journal_comment_notifications;

    /**
     * @OneToMany(targetEntity="JournalNotify", mappedBy="user")
     */
    protected $journal_notifications;

    /**
     * @OneToMany(targetEntity="Note", mappedBy="sender", fetch="EXTRA_LAZY")
     */
    protected $notes_sent;

    /**
     * @OneToMany(targetEntity="Note", mappedBy="recipient", fetch="EXTRA_LAZY")
     */
    protected $notes_received;

    /**
     * @OneToMany(targetEntity="Shout", mappedBy="sender", fetch="EXTRA_LAZY")
     */
    protected $shouts_sent;

    /**
     * @OneToMany(targetEntity="Shout", mappedBy="recipient", fetch="EXTRA_LAZY")
     */
    protected $shouts_received;

    /**
     * @OneToMany(targetEntity="ShoutNotify", mappedBy="user")
     */
    protected $shout_notifications;

    /**
     * @OneToMany(targetEntity="Upload", mappedBy="user", fetch="EXTRA_LAZY")
     */
    protected $uploads;

    /**
     * @OneToMany(targetEntity="UploadComment", mappedBy="user")
     */
    protected $upload_comments;

    /**
     * @OneToMany(targetEntity="UploadCommentNotify", mappedBy="user")
     */
    protected $upload_comment_notifications;

    /**
     * @OneToMany(targetEntity="UploadNotify", mappedBy="user")
     */
    protected $upload_notifications;

    /**
     * @OneToMany(targetEntity="TroubleTicket", mappedBy="user")
     */
    protected $trouble_tickets;

    /**
     * @OneToMany(targetEntity="TroubleTicket", mappedBy="assigned_to_user")
     */
    protected $trouble_tickets_assigned;

    /**
     * @OneToMany(targetEntity="TroubleTicketComment", mappedBy="user")
     */
    protected $trouble_ticket_comments;

    /**
     * @OneToMany(targetEntity="TroubleTicketNotify", mappedBy="user")
     */
    protected $trouble_ticket_notifications;

    /**
     * @OneToMany(targetEntity="UserComment", mappedBy="sender", fetch="EXTRA_LAZY")
     */
    protected $user_comments_sent;

    /**
     * @OneToMany(targetEntity="UserComment", mappedBy="recipient", fetch="EXTRA_LAZY")
     */
    protected $user_comments_received;

    /**
     * @OneToOne(targetEntity="UserContact", mappedBy="user")
     */
    protected $contact;

    /**
     * @OneToMany(targetEntity="UserSuspension", mappedBy="user")
     */
    protected $suspensions;

    /**
     * @OneToMany(targetEntity="UserSuspension", mappedBy="enforcer")
     */
    protected $suspensions_enforced;

    /**
     * @OneToMany(targetEntity="UserVariable", mappedBy="user", fetch="EAGER")
     */
    protected $vars;

    /**
     * @OneToMany(targetEntity="Watch", mappedBy="user", fetch="EXTRA_LAZY")
     */
    protected $watches;

    /**
     * @OneToMany(targetEntity="WatchNotify", mappedBy="user")
     */
    protected $watch_notifications;

    /**
     * Object Functions
     */

    protected $_vars;

    public function getVariables()
    {
        if (!$this->_vars)
        {
            $var_definitions = UserVariable::getDefinitions();

            $this->_vars = array();
            $vars_by_id = array();
            foreach($var_definitions as $var_key => $var_info)
            {
                $vars_by_id[$var_info['var_id']] = $var_key;
                $this->_vars[$var_key] = $var_info['default'];
            }

            if ($this->vars->count())
            {
                foreach($this->vars as $var_obj)
                {
                    $var_key = $vars_by_id[$var_obj->var_id];
                    $this->_vars[$var_key] = $var_obj->content;
                }
            }
        }

        return $this->_vars;
    }

    public function getVariable($var_key)
    {
        $vars = $this->getVariables();

        if (isset($vars[$var_key]))
            return $vars[$var_key];
        else
            return NULL;
    }

    public function setVariable($var_key, $value)
    {
        return $this->setVariables(array($var_key => $value));
    }

    public function setVariables($vars)
    {
        $em = $this->getEntityManager();
        $var_definitions = UserVariable::getDefinitions();

        $vars_to_set = array();
        foreach((array)$vars as $var_key => $var_new_value)
        {
            $var_id = $var_definitions[$var_key]['var_id'];
            $vars_to_set[$var_id] = $var_new_value;

            $this->_vars[$var_key] = $var_new_value;
        }

        // Updating existing variables.
        if ($this->vars->count())
        {
            foreach($this->vars as $var_obj)
            {
                $var_id = $var_obj->var_id;

                if (isset($vars_to_set[$var_id]))
                {
                    $var_obj->content = $vars_to_set[$var_id];
                    $em->persist($var_obj);

                    unset($vars_to_set[$var_id]);
                }
            }
        }

        // Create all remaining variables.
        foreach($vars_to_set as $var_id => $var_value)
        {
            $var_obj = new UserVariable;
            $var_obj->fromArray(array(
                'user'      => $this,
                'var_id'    => $var_id,
                'content'   => $var_value,
            ));
            $em->persist($var_obj);
        }

        $em->flush();
    }

    public function deleteVariable($var_key)
    {
        $var_definitions = UserVariable::getDefinitions();

        if (!isset($var_definitions[$var_key]))
            return;

        $var_id = $var_definitions[$var_key]['var_id'];

        $var_obj = UserVariable::getRepository()->findOneBy(array('user_id' => $this->id, 'var_id' => $var_id));
        if ($var_obj instanceof UserVariable)
            $var_obj->delete();

        unset($this->_vars[$var_key]);
    }
    
    /**
     * Returns the total comments a user has.
     * Excludes Trouble Ticket comments as those SHOULDN'T be publicly viewable.
     *
     * @return integer
     */
    public function getTotalComments() {
        return count($this->journal_comments) + count($this->upload_comments) + count($this->user_comments_sent);
    }

    /**
     * Static Functions
     */

    /**
     * Attempt to authenticate a user with a given username and password.
     *
     * @param $username
     * @param $password
     * @return bool|null|User
     */
    public static function authenticate($username, $password)
    {
        $login_info = self::getRepository()->findOneBy(array('username' => $username));

        if (!($login_info instanceof self))
            return FALSE;

        // Log in using new PHP-internal password authentication.
        if (!empty($login_info->auth_password_new) && substr($login_info->auth_password_new, 0, 1) == '$')
        {
            if (password_verify($password, $login_info->auth_password_new))
            {
                if (password_needs_rehash($login_info->auth_password_new, \PASSWORD_DEFAULT))
                    $login_info->setPassword($password)->save();

                return $login_info;
            }
        }
        else
        {
            // Attempt login with legacy password style, and then force a set to the new password style if successful.
            $hashed_password = crypt($password, '$2a$07$'.sha1('d67c5cbf5b01c9f91932e3b8def5e5f8').'$');
            $hashed_password = sha1($hashed_password);

            if (strcasecmp($hashed_password, $login_info->auth_password_legacy_hash) == 0)
            {
                // Force reset of password into newer format.
                $login_info->setPassword($password)->save();

                return $login_info;
            }
        }

        return FALSE;
    }

    /**
     * Returns an array of notification types and the shortened abbreviations for each.
     *
     * @return array
     */
    public static function getNotificationTypes()
    {
        static $notify_types;

        if (!$notify_types)
        {
            $di = \Phalcon\Di::getDefault();
            $url = $di['url'];

            $notify_types = array(
                'notify_favorites' => array(
                    'relation'  => 'favorite_notifications',
                    'short'     => 'favorite',
                    'abbr'      => 'F',
                    'title'     => 'Favorite',
                    'url'       => $url->route(['module' => 'account', 'controller' => 'messages', 'action' => 'others']).'#favorites',
                ),
                /*
                'journal_comment_notifications' => array(
                    'short'     => 'journal_comment',
                    'abbr'      => 'JC',
                    'title'     => 'Journal Comment',
                ),
                */
                'notify_journals'  => array(
                    'relation'  => 'journal_notifications',
                    'short'     => 'journal',
                    'abbr'      => 'J',
                    'title'     => 'Journal',
                    'url'       => $url->route(['module' => 'account', 'controller' => 'messages', 'action' => 'others']).'#journals',
                ),
                'notify_notes'     => array(
                    'relation'  => 'notes_received',
                    'short'     => 'note',
                    'abbr'      => 'N',
                    'title'     => 'Note',
                    'url'       => $url->route(['module' => 'account', 'controller' => 'messages', 'action' => 'pms']),
                ),
                'notify_shouts'    => array(
                    'relation'  => 'shout_notifications',
                    'short'     => 'shout',
                    'abbr'      => 'SH',
                    'title'     => 'Shout',
                    'url'       => $url->route(['module' => 'account', 'controller' => 'messages', 'action' => 'others']).'#shouts',
                ),
                'notify_comments'  => array(
                    'relation'  => 'upload_comment_notifications',
                    'short'     => 'upload_comment',
                    'abbr'      => 'C',
                    'title'     => 'Comment',
                    'url'       => $url->route(['module' => 'account', 'controller' => 'messages', 'action' => 'others']).'#comments',
                ),
                'notify_uploads'   => array(
                    'relation'  => 'upload_notifications',
                    'short'     => 'upload',
                    'abbr'      => 'S',
                    'title'     => 'Upload',
                    'url'       => $url->route(['module' => 'account', 'controller' => 'messages', 'action' => 'uploads']),
                ),
                'notify_tickets'   => array(
                    'relation'  => 'trouble_ticket_notifications',
                    'short'     => 'trouble_ticket',
                    'abbr'      => 'TT',
                    'title'     => 'Trouble Ticket',
                    'url'       => $url->route(['module' => 'account', 'controller' => 'messages', 'action' => 'troubletickets']),
                ),
                'notify_watches'   => array(
                    'relation'  => 'watch_notifications',
                    'short'     => 'watch',
                    'abbr'      => 'W',
                    'title'     => 'Watch',
                    'url'       => $url->route(['module' => 'account', 'controller' => 'messages', 'action' => 'others']).'#watches',
                ),
            );
        }

        return $notify_types;
    }

    /**
     * Return the displayed symbol for the user's access level.
     *
     * @param $access_level
     * @return mixed
     */
    public static function getUserSymbol($access_level)
    {
        $symbols = array(
            self::LEGACY_ACL_REGULAR        => '~',
            self::LEGACY_ACL_ADMINISTRATOR  => '@',
            self::LEGACY_ACL_TESTER         => '=',
            self::LEGACY_ACL_DEVELOPER      => '^',
            self::LEGACY_ACL_BANNED         => '-',
            self::LEGACY_ACL_DECEASED       => html_entity_decode('&#8734;'),
        );

        if (isset($symbols[$access_level]))
            return $symbols[$access_level];
        else
            return $symbols[self::LEGACY_ACL_REGULAR];
    }

    /**
     * Return the text version of the user's access level.
     *
     * @param $access_level
     * @return mixed
     */
    public static function getUserStatus($access_level)
    {
        $names = array(
            self::LEGACY_ACL_REGULAR        => 'Member',
            self::LEGACY_ACL_ADMINISTRATOR  => 'Administrator',
            self::LEGACY_ACL_TESTER         => 'Tester',
            self::LEGACY_ACL_DEVELOPER      => 'Developer',
            self::LEGACY_ACL_BANNED         => 'Banned',
            self::LEGACY_ACL_DECEASED       => 'Deceased',
        );

        if (isset($names[$access_level]))
            return $names[$access_level];
        else
            return $names[self::LEGACY_ACL_REGULAR];
    }

    /**
     * Return the lower-case, filtered version of a username (for URLs).
     *
     * @param $username
     * @return mixed|string
     */
    public static function getLowerCase($username)
    {
        $username = strtolower($username);
        $username = substr(str_replace(array('_', ' ', '#', '!'), '', $username), 0, 30);
        return $username;
    }

    /**
     * Return an avatar for the specified user information, based on the logged-in
     * user's settings.
     *
     * @param $lower
     * @param $avatar_mtime
     * @param bool|false $force_show
     * @return string
     */
    public static function getUserAvatar($lower, $avatar_mtime = null, $force_show = false)
    {
        $di = \Phalcon\Di::getDefault();
        $show_avatar = true;

        if (!$avatar_mtime)
            $avatar_mtime = time();

        if (!$force_show)
        {
            // Check whether the current user has disabled viewing avatars.
            $user = $di['user'];
            $show_avatar = ($user->getVariable('disable_avatars') != 1) ? true : false;
        }

        if ($show_avatar)
        {
            $avatar_dir = $di['config']->application->avatars_path;
            $avatar_url = $di['config']->application->avatars_url;

            // $avatar_base = $avatar_mtime . '/' . $lower . '.gif';
            $avatar_base = $lower . '.gif';

            if (file_exists($avatar_dir.'/'.$avatar_base))
                return $avatar_url.'/'.$avatar_base.'?'.$avatar_mtime;
        }

        $default_avatar = $di['url']->getStatic('img/avatar.gif');
        return $default_avatar;
    }

    /**
     * Add one to a "counter" field for a user.
     *
     * @param string $counter The counter field to use.
     * @param int $user_id The user ID to update.
     */
    public static function incrementCounter($counter, $user_id)
    {
        $em = self::getEntityManager();

        $field_name = 'us.num_'.$counter;

        $em->createQuery('UPDATE '.__CLASS__.' us SET '.$field_name.'='.$field_name.'+1 WHERE us.id = :user_id')
            ->setParameter('user_id', $user_id)
            ->execute();
    }

    /**
     * Remove one from a "counter" field for a user.
     *
     * @param string $counter The counter field to use.
     * @param int $user_id The user ID to update.
     */
    public static function decrementCounter($counter, $user_id)
    {
        $em = self::getEntityManager();

        $field_name = 'us.num_'.$counter;

        $em->createQuery('UPDATE '.__CLASS__.' us SET '.$field_name.'=IF('.$field_name.'=0, 0, '.$field_name.'-1) WHERE us.id = :user_id')
            ->setParameter('user_id', $user_id)
            ->execute();
    }
}