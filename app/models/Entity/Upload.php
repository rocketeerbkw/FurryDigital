<?php
namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Uploads
 *
 * @Table(name="submissions", indexes={
 *   @Index(name="user", columns={"user"}),
 *   @Index(name="adultsubmission_isscrap", columns={"adultsubmission", "isscrap"}),
 *   @Index(name="species", columns={"species"}),
 *   @Index(name="category_adultsubmission", columns={"category", "adultsubmission"}),
 *   @Index(name="subtype_species_adultsubmission_isscrap", columns={"subtype", "species", "adultsubmission", "isscrap"}),
 *   @Index(name="category", columns={"category"}),
 *   @Index(name="date", columns={"date"})
 * })
 * @Entity
 * @HasLifecycleCallbacks
 */
class Upload extends \FA\Doctrine\Entity
{
    const TYPE_IMAGE = 1; // Submissions
    const TYPE_TEXT  = 2; // Stories
    const TYPE_AUDIO = 4; // Music
    const TYPE_VIDEO = 5; // Video
    
    const RATING_GENERAL = 0;
    const RATING_MATURE = 2;
    const RATING_ADULT = 1;

    public function __construct()
    {
        $this->created_at = time();
        $this->updated_at = time();

        $this->favorites = new ArrayCollection;
        $this->comments = new ArrayCollection;
        $this->notifications = new ArrayCollection;
        $this->comment_notifications = new ArrayCollection;
        $this->folders = new ArrayCollection;
    }

    /**
     * @PreUpdate
     */
    public function updating()
    {
        $this->updated_at = time();
    }

    /**
     * @PostPersist
     */
    public function created()
    {
        User::incrementCounter('uploads', $this->user_id);
        \FA\Legacy\Notifications::dispatch('upload', $this->id);
    }

    /**
     * @PreDelete
     */
    public function deleting()
    {
        // Delete files
        $full_path = $this->getFullPath();
        if (!empty($full_path))
            @unlink($full_path);

        $small_path = $this->getSmallPath();
        if (!empty($small_path))
            @unlink($small_path);

        $thumb_path = $this->getThumbnailPath();
        if (!empty($thumb_path))
            @unlink($thumb_path);

        if ($this->comments->count() > 0)
        {
            foreach($this->comments as $comment)
            {
                \FA\Legacy\Notifications::purge('upload_comment', $comment->id, $this->user_id);
            }
        }

        User::decrementCounter('uploads', $this->user_id);
        \FA\Legacy\Notifications::purge('upload', $this->id);
    }

    /**
     * @var integer
     * @Column(name="rowid", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var integer
     * @Column(name="user", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     */
    protected $user_id;

    /**
     * @ManyToOne(targetEntity="User", inversedBy="uploads")
     * @JoinColumns({
     *   @JoinColumn(name="user", referencedColumnName="userid", onDelete="CASCADE")
     * })
     */
    protected $user;

    /**
     * @var int
     * @Column(name="submission_type", type="smallint", nullable=false)
     */
    protected $upload_type;

    /**
     * Return the text name of the upload type.
     * @return string
     */
    public function getUploadTypeName()
    {
        return self::getUploadTypeText($this->upload_type);
    }

    /**
     * @var integer UNIX timestamp when the record was created.
     * @Column(name="date", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     */
    protected $created_at;

    /**
     * @var integer Most recently updated UNIX timestamp.
     * @Column(name="date_updated", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     */
    protected $updated_at;

    /**
     * @var string
     * @Column(name="title", type="string", options={"default"=""}, length=60)
     */
    protected $title = '';

    /**
     * @var string
     * @Column(name="message", type="text", length=65535)
     */
    protected $description = '';

    /**
     * @var integer
     * @Column(name="category", type="smallint", nullable=false)
     */
    protected $category = 0;
    
    /**
     * Get a human-readable version of the category value.
     *
     * @return string
     */
    public function getCategoryReadable()
    {
        $di = \Phalcon\Di::getDefault();
        $config = $di['config'];
        
        return self::_getReadable($this->category, $config->fa->categories->toArray());
    }

    /**
     * @var integer
     * @Column(name="species", type="smallint", nullable=false)
     */
    protected $species = 1;
    
    /**
     * Get a human-readable version of the species value.
     *
     * @return string
     */
    public function getSpeciesReadable()
    {
        $di = \Phalcon\Di::getDefault();
        $config = $di['config'];
        
        return self::_getReadable($this->species, $config->fa->species->toArray());
    }

    /**
     * @var integer
     * @Column(name="subtype", type="smallint", nullable=false)
     */
    protected $subtype = 1;
    
    /**
     * Get a human-readable version of the subtype value.
     *
     * @return string
     */
    public function getSubtypeReadable()
    {
        $di = \Phalcon\Di::getDefault();
        $config = $di['config'];
        
        return self::_getReadable($this->subtype, $config->fa->art_types->toArray());
    }

    /**
     * @var string
     * @Column(name="gender", type="string", options={"default"=""}, length=40, nullable=false)
     */
    protected $gender = 0;
    
    /**
     * Get a human-readable version of the gender value.
     *
     * @return string
     */
    public function getGenderReadable()
    {
        $di = \Phalcon\Di::getDefault();
        $config = $di['config'];
        
        return self::_getReadable($this->gender, $config->fa->genders->toArray());
    }

    /**
     * @var string The base path of the file.
     * @Column(name="url", type="string", options={"default"=""}, length=200, nullable=false)
     */
    protected $full = '';

    /**
     * @var string UUID for file
     * @Column(name="file_uuid", type="binary_uuid", nullable=true)
     */
    protected $full_uuid = 0;

    /**
     * TODO: Unused Field
     * @var boolean
     * @Column(name="file_mimetype_id", type="smallint", nullable=false)
     */
    protected $full_mimetype_id = 0;

    public function getFullPath()
    {
        return self::getFilePath($this->_getFull());
    }

    public function getFullUrl()
    {
        return self::getFileUrl($this->_getFull());
    }

    public function setFull($new_path)
    {
        // Delete existing file (if exists).
        if (!empty($this->full))
        {
            @unlink(self::getFilePath($this->full));
        }

        // Clear legacy files.
        if (!empty($this->_unused_story_path))
        {
            @unlink(self::getFilePath($this->_unused_story_path));
            $this->_unused_story_path = NULL;
        }
        if (!empty($this->_unused_poetry_path))
        {
            @unlink(self::getFilePath($this->_unused_poetry_path));
            $this->_unused_poetry_path = NULL;
        }
        if (!empty($this->_unused_music_path))
        {
            @unlink(self::getFilePath($this->_unused_music_path));
            $this->_unused_music_path = NULL;
        }

        $this->full = self::cleanUpBasePath($new_path);
        $this->full_uuid = \FA\Legacy\Utilities::uuid();
    }

    protected function _getFull()
    {
        if ($this->upload_type != self::TYPE_IMAGE)
        {
            if (!empty($this->_unused_story_path))
                return $this->_unused_story_path;

            if (!empty($this->_unused_poetry_path))
                return $this->_unused_poetry_path;

            if (!empty($this->_unused_music_path))
                return $this->_unused_music_path;
        }

        return $this->full;
    }

    /**
     * TODO: Unused Field
     * @var string Legacy path for stories.
     * @Column(name="story", type="string", options={"default"=""}, length=200, nullable=false)
     */
    protected $_unused_story_path = '';

    /**
     * TODO: Unused Field
     * @var string Legacy path for poetry.
     * @Column(name="poetry", type="string", options={"default"=""}, length=200, nullable=false)
     */
    protected $_unused_poetry_path = '';

    /**
     * TODO: Unused Field
     * @var string Legacy path for music.
     * @Column(name="musicfile", type="string", options={"default"=""}, length=200, nullable=false)
     */
    protected $_unused_music_path = '';

    /**
     * @var string The base path of the smaller version of the file.
     * @Column(name="smallerurl", type="string", options={"default"=""}, length=200, nullable=false)
     */
    protected $small = '';

    public function getSmallPath()
    {
        return self::getFilePath($this->small);
    }

    public function getSmallUrl()
    {
        return self::getFileUrl($this->small);
    }

    public function setSmall($new_path)
    {
        // Delete existing file (if exists).
        if (!empty($this->small))
            @unlink(self::getFilePath($this->small));

        $this->small = self::cleanUpBasePath($new_path);
    }

    /**
     * @var string The base path of the
     * @Column(name="thumbnail", type="string", options={"default"=""}, length=200, nullable=false)
     */
    protected $thumbnail = '';

    /**
     * @var string UUID for thumbnail
     * @Column(name="thumb_uuid", type="binary_uuid", nullable=true)
     */
    protected $thumbnail_uuid = 0;

    /**
     * TODO: Unused Field
     * @var boolean
     * @Column(name="thumb_mimetype_id", type="boolean", nullable=false)
     */
    protected $thumbnail_mimetype_id = 0;

    public function getThumbnailPath()
    {
        return self::getFilePath($this->thumbnail);
    }

    public function getThumbnailUrl()
    {
        return self::getFileUrl($this->thumbnail);
    }

    public function setThumbnail($new_path)
    {
        // Delete existing file (if exists).
        if (!empty($this->thumbnail))
            @unlink(self::getFilePath($this->thumbnail));

        $this->thumbnail = self::cleanUpBasePath($new_path);
        $this->thumbnail_uuid = \FA\Legacy\Utilities::uuid();
    }

    /**
     * @var integer
     * @Column(name="views", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     */
    protected $views = 0;

    /**
     * @var integer Cached number of people watching the image.
     * @Column(name="numtracked", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     */
    protected $num_tracked = 0;

    /**
     * @var integer Cached number of comments.
     * @Column(name="comments", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     */
    protected $num_comments = 0;

    /**
     * @var boolean
     * @Column(name="adultsubmission", type="smallint", nullable=false)
     */
    protected $rating = 0;

    /**
     * Get a human-readable version of the rating number.
     *
     * @return string
     */
    public function getRatingReadable()
    {
        return self::getRatingText($this->rating);
    }

    /**
     * Get the proper image for the current image rating.
     *
     * @return mixed
     */
    public function getRatingImage()
    {
        $di = \Phalcon\Di::getDefault();
        $url = $di['url'];
    
        return $url->getStatic('img/labels/' . $this->getRatingReadable() . '.gif');
    }

    /**
     * Indicate whether the user is currently allowed to see the current piece of art.
     *
     * @return bool
     */
    public function canSee()
    {
        $di = \Phalcon\Di::getDefault();
        $fa = $di['fa'];

        return (bool)$fa->canSeeArt($this->getRatingReadable());
    }

    /**
     * @var integer The width of the image (if uploaded)
     * @Column(name="width", type="smallint", nullable=false)
     */
    protected $width = 0;

    /**
     * @var integer The height of the image (if uploaded)
     * @Column(name="height", type="smallint", nullable=false)
     */
    protected $height = 0;

    /**
     * @var boolean Whether the submission falls into the "Scraps" folder.
     * @Column(name="isscrap", type="boolean", nullable=false)
     */
    protected $is_scrap = false;

    /**
     * @var string
     * @Column(name="keywords", type="string", options={"default"=""}, length=255, nullable=false)
     */
    protected $keywords = '';
    
    /**
     * Returns the keywords as an array. If empty, returns FALSE.
     *
     * @return array
     */
    public function getKeywords()
    {
        return (!empty($this->keywords) ? preg_split('![\s]+!', $this->keywords) : false);
    }

    /**
     * @var integer
     * @Column(name="type", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     */
    protected $type = 0;

    /**
     * @var boolean
     * @Column(name="is_deleted", type="boolean", nullable=false)
     */
    protected $is_deleted = false;

    /**
     * @var boolean
     * @Column(name="is_hidden", type="boolean", nullable=false)
     */
    protected $is_hidden = false;

    /**
     * @var boolean
     * @Column(name="is_locked", type="boolean", nullable=false)
     */
    protected $is_locked = false;

    /**
     * @var integer
     * @Column(name="comments_locked", type="boolean", nullable=false)
     */
    protected $comments_locked = false;

    /**
     * @var integer
     * @Column(name="lock_id", type="integer", length=11, options={"unsigned"=true}, nullable=false)
     */
    protected $lock_id = 0;

    /**
     * @OneToMany(targetEntity="Favorite", mappedBy="upload")
     */
    protected $favorites;

    /**
     * @OneToMany(targetEntity="UploadComment", mappedBy="upload")
     */
    protected $comments;

    /**
     * @OneToMany(targetEntity="UploadCommentNotify", mappedBy="upload")
     */
    protected $comment_notifications;

    /**
     * @OneToMany(targetEntity="UploadFolder", mappedBy="upload")
     */
    protected $folders;

    /**
     * @OneToMany(targetEntity="UploadNotify", mappedBy="upload")
     */
    protected $notifications;

    /**
     * Given the filename of the uploaded file, return all generated paths.
     *
     * @param $uploaded_file
     * @return array
     */
    public function generatePaths($uploaded_file)
    {
        $file_info = pathinfo($uploaded_file);

        // Clean up the provided file path.
        $filename_base = preg_replace('#[^a-zA-Z0-9\_]#', '', $file_info['filename']);
        $filename_base = substr($filename_base, 0, 100);

        // Determine the folder of the submission by type.
        $config = self::getConfig();
        $types = $config->fa->upload_types->toArray();

        if (!empty($types[$this->upload_type]['folder']))
            $folder = $types[$this->upload_type]['folder'];
        else
            $folder = $types[self::TYPE_IMAGE]['folder'];

        $path_prefix = $this->user->lower.'/'.$folder.'/'.$this->id;
        $path_suffix = $filename_base.'.'.$file_info['extension'];

        $base_paths = array(
            'full'      => $path_prefix.'.'.$path_suffix,
            'small'     => $path_prefix.'.half.'.$path_suffix,
            'thumbnail' => $path_prefix.'.thumbnail.'.$path_suffix,
        );

        $return_paths = array();
        foreach($base_paths as $path_key => $path_base)
        {
            $return_paths[$path_key] = array(
                'base'      => $path_base,
                'path'      => self::getFilePath($path_base),
                'url'       => self::getFileUrl($path_base),
            );
        }
        return $return_paths;
    }
    
    /**
     * Returns the main content's extension(ie mp3)
     *
     * @return string
     */
    public function getExtension() {    
        return substr(strrchr($this->full, '.'), 1);
    }
    
    /**
     * Returns the main content's MIME type
     *
     * @return string
     */
    public function getMIME() {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        
        // Get the MIME!
        $mime = finfo_file($finfo, $this->full);
        
        finfo_close($finfo);
    
        return $mime;
    }

    /**
     * Static Functions
     */
     
    /**
     * Used internal to avoid repeating this like 4 times
     *
     * @param $value
     * @param $haystack
     * @return string
     */
    private static function _getReadable($value, $haystack) {
        $readable = \FA\Utilities::recursive_array_search($value, $haystack);
        
        return ($readable ? $readable : 'N/A');
    }

    public static function getFileUrl($file_base_path)
    {
        $config = self::getConfig();

        $art_url = $config->application->art_url;
        return $art_url.'/'.self::cleanUpBasePath($file_base_path);
    }

    public static function getFilePath($file_base_path)
    {
        $config = self::getConfig();

        $art_path = $config->application->art_path;
        return $art_path.'/'.self::cleanUpBasePath($file_base_path);
    }

    public static function cleanUpBasePath($file_base_path)
    {
        $file_base_path = str_replace('./art/', '', $file_base_path);
        return ltrim($file_base_path, '/');
    }

    public static function getRatingText($rating_code)
    {
        $rating_names = array(
            self::RATING_GENERAL    => 'general',
            self::RATING_MATURE     => 'mature',
            self::RATING_ADULT      => 'adult',
        );

        if (isset($rating_names[$rating_code]))
            return $rating_names[$rating_code];
        else
            return $rating_names[self::RATING_GENERAL];
    }

    public static function getUploadTypeText($upload_type)
    {
        $upload_types = array(
            self::TYPE_IMAGE        => 'image',
            self::TYPE_TEXT         => 'text',
            self::TYPE_AUDIO        => 'audio',
            self::TYPE_VIDEO        => 'video',
        );

        if (isset($upload_types[$upload_type]))
            return $upload_types[$upload_type];
        else
            return $upload_types[self::TYPE_IMAGE];
    }

    public static function getConfig()
    {
        static $config;

        if (!$config)
        {
            $di = \Phalcon\Di::getDefault();
            $config = $di['config'];
        }

        return $config;
    }
}