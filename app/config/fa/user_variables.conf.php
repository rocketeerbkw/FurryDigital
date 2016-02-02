<?php
// all variables must be defined as "name" => array("var_id", "desc", "default", "allowed") pairs
// "var_id" must be unique
// "allowed" can take several forms:
// '*' (string)                   means all values are acceptable
// '/regex/' (string)             means only values that pass the regex are acceptable
// array('val1', 'val2', 'val3')  means only specified values are acceptable

return array(

    /*
     * user settings
     */
    'perpage' => array(
        'var_id'  => 1,
        'desc'    => 'Wherever paginated content output is involved, this setting determines the default per-page value used',
        'default' => 36,
        'allowed' => array(24, 36, 48, 60)
    ),
    'disable_avatars' => array(
        'var_id'  => 2,
        'desc'    => 'Replaces avatars on all pages with the default "noavatar" image, saving bandwidth',
        'default' => 0,
        'allowed' => array(0, 1)
    ),
    'date_format' => array(
        'var_id'  => 3,
        'desc'    => 'Controls the formatting of the dates displayed everywhere on the site',
        'default' => 'fuzzy',
        'allowed' => array('fuzzy', 'full')
    ),
    'search_order' => array(
        'var_id'  => 4,
        'desc'    => 'Sets the preffered search result display order',
        'default' => 'relevancy',
        'allowed' => array('relevancy', 'date', 'popularity')
    ),
    'search_direction' => array(
        'var_id'  => 5,
        'desc'    => 'Sets the preffered search result display order direction',
        'default' => 'desc',
        'allowed' => array('asc', 'desc')
    ),
    'newsubmissions_direction' => array(
        'var_id'  => 6,
        'desc'    => 'Controls how new submission notifications are displayed - oldest or newest first',
        'default' => 'desc',
        'allowed' => array('asc', 'desc')
    ),
    'thumbnail_size' => array(
        'var_id'  => 17,
        'desc'    => 'Controls the size of the displayed thumbnails',
        'default' => 200,
        'allowed' => array(100, 150, 200)
    ),
    'gallery_navigation' => array(
        'var_id'  => 22,
        'desc'    => 'Preference for the type of gallery navigation on the submission view page',
        'default' => 'minigallery',
        'allowed' => array('minigallery', 'links')
    ),

    /*
     * privacy features
     */

    /* personal */
    'ssl_enable' => array(
        'var_id'  => 18,
        'desc'    => 'Enable Whole-Site SSL.',
        'default' => 'default',
        'allowed' => array('default', 'on', 'off')
    ),

    'featured_journal_id' => array(
        'var_id'  => 25,
        'desc'    => 'ID of the journal a user has decided to feature. Featured journals will be displayed on the userpage. If no featured journal is set, the latest journal will be displayed instead.',
        'default' => 0,
        'allowed' => '/[0-9]+/'
    ),

    /*
     * privacy features
     */

    /* personal */
    'account_disabled' => array(
        'var_id'  => 19,
        'desc'    => "Disable user's account. (Only hides userpage.)",
        'default' => 0,
        'allowed' => '/[0-9]+/'
    ),

    /* feature  disable */
    'no_guests' => array(
        'var_id'  => 9,
        'desc'    => "Prevent guests from viewing any of the user's pages.",
        'default' => 0,
        'allowed' => array(0, 1)
    ),
    'no_notes' => array(
        'var_id'  => 10,
        'desc'    => 'Disables the notes system, both for receiving and sending notes from and to anyone but the administration.',
        'default' => 0,
        'allowed' => array(0, 1)
    ),
    'hide_favorites' => array(
        'var_id'  => 11,
        'desc'    => 'Controls what kind of favorites on your account not to show publically. Options are: Hide everything(e), Hide adult+mature(am), Hide adult(a) or Show everything(n)',
        'default' => 'n',
        'allowed' => array('e', 'ma', 'a', 'n') /* everything, adult+mature, adult, none */
    ),

    /*
     * admin
     */

    /* wrt other admins */
    'AP.canEditAP' => array(
        'var_id'  => 12,
        'desc'    => "This is god mode. Don't set it unless you want to give the user access to everything in the Admin CP. (Requires admin status to work, though.)",
        'default' => 0,
        'allowed' => array(0, 1)
    ),
    'AP.canChangeAdmin' => array(
        'var_id'  => 13,
        'desc'    => 'Allow an admin to grant or revoke admin status from other user as well as editing other admin accounts.',
        'default' => 0,
        'allowed' => array(0, 1)
    ),
    'AP.canIPBan' => array(
        'var_id'  => 14,
        'desc'    => 'Allow an admin to enact or remove IP bans.',
        'default' => 0,
        'allowed' => array(0, 1)
    ),
    'AP.canReadUserNotes' => array(
        'var_id'  => 21,
        'desc'    => 'User can always read other user\'s notes.',
        'default' => 0,
        'allowed' => array(0, 1)
    ),
    /* wrt regular users */
    'disable_shouts' => array(
        'var_id'  => 8,
        'desc'    => "Prevent users from shouting on the user's page.",
        'default' => 0,
        'allowed' => array(0, 1)
    ),
    'remove_user_from_search' => array(
        'var_id'  => 15,
        'desc'    => "Prevent user's submissions from being indexed by the search engine. Changing this will affect new submissions immediately, for previous submissions it may take up to 24 hours for the change to take effect.",
        'default' => 0,
        'allowed' => array(0, 1)
    ),

    /*
     * misc
     */
    'hide_online_counter' => array(
        'var_id'  => 16,
        'desc'    => 'Speed up page access by disabling online counter. (Deprecated.)',
        'default' => 0,
        'allowed' => array(0, 1)
    ),

    /*
     * readonly
     */
    'RO.adminsCanSeeNotes' => array(
        'var_id'  => 20,
        'desc'    => 'Ticket ID that user consented to allowing their notes to be read.',
        'default' => 0,
        'allowed' => '/[0-9]+/'
    )
);