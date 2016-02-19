<?php
use App\Utilities;
use Entity\Upload;

$di = \Phalcon\Di::getDefault();
$var_config = $di['config']->fd->user_variables->toArray();

return array(
    'method' => 'post',
    'groups' => array(

        'account' => array(
            'legend' => 'Account Settings',
            'elements' => array(

                'fullname' => array('text', array(
                    'label' => 'Display Name',
                    'description' => 'Modifies the "Display Name" which appears on your User Page. Does not change your login ID.',
                    'class' => 'half-width',
                    'belongsTo' => 'user',
                    'required' => true,
                )),

                'email' => array('text', array(
                    'label' => 'E-mail Address',
                    'description' => 'Be sure to keep your email address up-to-date! Without it, account recovery or password resets may not be possible.',
                    'class' => 'half-width',
                    'belongsTo' => 'user',
                    'required' => true,
                    'validators' => array('EmailAddress'),
                )),

                'timezone' => array('select', array(
                    'label' => 'Time Zone',
                    'description' => 'Updates the site\'s time zones to reflect your local time. Server default timezone is EST (UTC -5).',
                    'belongsTo' => 'user',
                    'default' => '-0500',
                    'options' => array(
                        '-1200' => 'International Date Line West',
                        '-1100' => 'Samoa Standard Time',
                        '-1000' => 'Hawaiian Standard Time',
                        '-0900' => 'Alaskan Standard Time',
                        '-0800' => 'Pacific Standard Time',
                        '-0700' => 'Mountain Standard Time',
                        '-0600' => 'Central Standard Time',
                        '-0500' => 'Eastern Standard Time',
                        '-0430' => 'Caracas Standard Time',
                        '-0400' => 'Atlantic Standard Time',
                        '-0330' => 'Newfoundland Standard Time',
                        '-0300' => 'Greenland Standard Time',
                        '-0200' => 'Mid-Atlantic Standard Time',
                        '-0100' => 'Cape Verde Standard Time',
                        '+0000' => 'Greenwich Mean Time',
                        '+0100' => 'W. Europe Standard Time',
                        '+0200' => 'E. Europe Standard Time',
                        '+0300' => 'Russian Standard Time',
                        '+0330' => 'Iran Standard Time',
                        '+0400' => 'Arabian Standard Time',
                        '+0430' => 'Afghanistan Standard Time',
                        '+0500' => 'West Asia Standard Time',
                        '+0530' => 'India Standard T ime',
                        '+0545' => 'Nepal Standard Time',
                        '+0600' => 'Central Asia Standard Time',
                        '+0630' => 'Myanmar Standard Time',
                        '+0700' => 'North Asia Standard Time',
                        '+0800' => 'North Asia East Standard Time',
                        '+0900' => 'Tokyo Standard Time',
                        '+0930' => 'Cen. Australia Standard Time',
                        '+1000' => 'West Pacific Standard Time',
                        '+1100' => 'Central Pacific Standard Time',
                        '+1200' => 'New Zealand Standard Time'
                    ),
                )),

                'birthday' => array('date', array(
                    'label' => 'Birthday',
                    'description' => 'Your date of birth. <b>Users found falsifying their age will have their mature art locked, and potentially face account closure.</b> Your date of birth will not be displayed publicly.',
                    'belongsTo' => 'user',
                    'min'   => date('Y-m-d', strtotime('-100 years')),
                    'max'   => date('Y-m-d', strtotime('-5 years')),
                    'required' => true,
                )),

                'seeadultart' => array('radio', array(
                    'label' => 'Enable Adult Artwork',
                    'description' => 'By enabling Mature or Adult artwork you agree that you are of legal age in the United States (18+). In compliance with United States law, FurryDigital will lock any account found lying/misrepresenting their age and prevent them from further viewing of adult work.',
                    'belongsTo' => 'user',
                    'default' => '0',
                    'options' => array(
                        Upload::RATING_GENERAL  => 'General',
                        Upload::RATING_MATURE   => 'General and Mature',
                        Upload::RATING_ADULT    => 'General, Mature, and Adult',
                    ),
                )),

                'new_password' => array('password', array(
                    'label' => 'Change Password',
                    'description' => 'Leave this field blank to keep your current password.',
                )),

                'new_password_confirm' => array('password', array(
                    'label' => 'Confirm New Password',
                    'description' => 'Enter your password again to confirm.',
                )),

            ),
        ),

        'global_site_settings' => array(
            'legend' => 'Global Site Settings',
            'elements' => array(

                'account_disabled' => array('radio', array(
                    'label' => 'Disable Account',
                    'description' => 'This will prevent your userpage, gallery, favorites and journals from being shown to other users. While disabled, you will not be able to post or upload content to the community.',
                    'belongsTo' => 'vars',
                    'options' => array(
                        0 => 'Account Enabled',
                        1 => 'Account Disabled',
                    ),
                )),

                'disable_avatars' => array('radio', array(
                    'label' => 'Disable Avatars',
                    'description' => 'Disabling avatars replaces avatars on the site with the default user icon.',
                    'belongsTo' => 'vars',
                    'options' => array(
                        0       => 'Enable Avatars',
                        1       => 'Disable Avatars',
                    ),
                )),

                'date_format' => array('radio', array(
                    'label' => 'Date Format',
                    'description' => 'This setting specifies the default format the site uses for dates. You can switch between date formats on-the-fly by clicking the any displayed date on the site.',
                    'options' => array(
                        'full'  => 'Full Date (October 30, 2015 00:23)',
                        'fuzzy' => 'Fuzzy Date (5 hours ago)',
                    ),
                    'belongsTo' => 'vars',
                )),

                'perpage' => array('select', array(
                    'label' => 'Submissions Per Page',
                    'description' => 'Change the default number of images which display on each page of multi-page galleries.',
                    'options' => Utilities::pairs($var_config['perpage']['allowed']),
                    'belongsTo' => 'vars',
                )),

                'newsubmissions_direction' => array('radio', array(
                    'label' => 'Notification Sort Order',
                    'description' => 'Change the order in which submission notifications are displayed.',
                    'options' => array(
                        'asc'       => 'Newest First',
                        'desc'      => 'Oldest First',
                    ),
                    'belongsTo' => 'vars',
                )),

                'thumbnail_size' => array('select', array(
                    'label' => 'Preferred Thumbnail Size',
                    'description' => 'This option has an effect on gallery pages, namely: artist\'s gallery or scraps, your favourites, browse and search results, and the message center.',
                    'options' => Utilities::pairs($var_config['thumbnail_size']['allowed']),
                    'belongsTo' => 'vars',
                )),

                'gallery_navigation' => array('radio', array(
                    'label' => 'Gallery Navigation Style',
                    'description' => 'A preference for how quick gallery navigation looks on the submission view pages.',
                    'options' => array(
                        'minigallery'   => 'Mini-Gallery',
                        'links'         => 'Prev/Next Links',
                    ),
                    'belongsTo' => 'vars',
                )),

            ),
        ),

        'privacy_settings' => array(
            'legend' => 'Privacy Settings',
            'elements' => array(

                'hide_favorites' => array('radio', array(
                    'label' => 'Hide Favorites',
                    'description' => 'Change which kind of favorites the public will see when viewing your profile. Note that you will always see all favorites regardless of this setting.',
                    'options' => array(
                        'n'     => 'Show All Favorites',
                        'a'     => 'Hide Adult Only',
                        'ma'    => 'Hide Mature and Adult',
                        'e'     => 'Hide All Favorites',
                    ),
                    'belongsTo' => 'vars',
                )),

                'no_guests' => array('radio', array(
                    'label' => 'Disable Guest Access',
                    'description' => 'Prevent guests from directly viewing your submissions, gallery, journals, favourites and profile page.',
                    'layout' => 'col50',
                    'options' => array(
                        0       => 'Allow Guests',
                        1       => 'Block Guests',
                    ),
                    'belongsTo' => 'vars',
                )),

                'no_notes' => array('radio', array(
                    'label' => 'Disable Notes System',
                    'description' => 'Enable or disable the ability to send or receive notes using App\'s note system. Note that administrators are never restricted from sending you notes.',
                    'layout' => 'col50',
                    'options' => array(
                        0       => 'Enable Notes',
                        1       => 'Disable Notes',
                    ),
                    'belongsTo' => 'vars',
                )),

            ),
        ),

        'submit' => array(
            'elements' => array(
                // CSRF forgery prevention.
                'csrf' => array('csrf'),

                'submit'        => array('submit', array(
                    'type'  => 'submit',
                    'label' => 'Update Account Settings',
                    'helper' => 'formButton',
                    'class' => 'btn btn-default',
                )),
            ),
        ),

    ),
);