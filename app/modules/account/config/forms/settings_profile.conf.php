<?php
$di = \Phalcon\Di::getDefault();
$config = $di['config'];

$form_config = array(
    'method' => 'post',
    'groups' => array(

        'user_page_info' => array(
            'legend' => 'User Page Info',
            'elements' => array(

                'profileinfo' => array('textarea', array(
                    'label' => 'Public Profile',
                    'belongsTo' => 'user',
                    'style' => 'min-height: 250px;',
                    'class' => 'full-width textarea textarearesize',
                )),

                'species' => array('text', array(
                    'label' => 'Your Species',
                    'belongsTo' => 'user',
                    'maxLength' => 255,
                )),

                'typeartist' => array('select', array(
                    'label' => 'Type of Artist/User',
                    'options' => array('' => 'Member') + $config->fa->artist_types->toArray(),
                    'belongsTo' => 'user',
                )),

                'fullview' => array('select', array(
                    'label' => 'View Images Full-Resolution by Default',
                    'options' => array(0 => 'No', 1 => 'Yes'),
                    'belongsTo' => 'user',
                )),

            ),
        ),

        'personal_info' => array(
            'legend' => 'Personal Info',
            'elements' => array(

                'mood' => array('select', array(
                    'label' => 'What Best Describes Your Mood?',
                    'options' => array('' => '') + $config->fa->moods->toArray(),
                    'belongsTo' => 'user',
                    'layout' => 'col33',
                )),

                'music' => array('text', array(
                    'label' => 'Favorite Music Genre(s)',
                    'description' => 'Indie Rock, Classical, Hip Hop...',
                    'belongsTo' => 'user',
                    'maxLength' => 255,
                    'layout' => 'col33',
                )),

                'favoritemovie' => array('text', array(
                    'label' => 'Favorite Films',
                    'description' => 'The Lego Movie, Pacific Rim, The Big Lebowski...',
                    'belongsTo' => 'user',
                    'maxLength' => 255,
                    'layout' => 'col33',
                )),

                'favoritegame' => array('text', array(
                    'label' => 'Favorite Game',
                    'description' => 'Team Fortress 2, Dark Souls, Halo...',
                    'belongsTo' => 'user',
                    'maxLength' => 255,
                    'layout' => 'col33',
                )),

                'favoriteplatform' => array('text', array(
                    'label' => 'Preferred Gaming Platform(s)',
                    'description' => 'Xbox One, PlayStation 4, Wii U, PC...',
                    'belongsTo' => 'user',
                    'maxLength' => 255,
                    'layout' => 'col33',
                )),

                'favoriteartist' => array('text', array(
                    'label' => 'Favorite Artist(s)',
                    'description' => 'You may use :iconusername: here.',
                    'belongsTo' => 'user',
                    'maxLength' => 255,
                    'layout' => 'col33',
                )),

                'favoriteanimal' => array('text', array(
                    'label' => 'Favorite Animal(s)',
                    'description' => 'Hyenas, dragons, foxes, etc...',
                    'belongsTo' => 'user',
                    'maxLength' => 255,
                    'layout' => 'col33',
                )),

                'favoritewebsite' => array('text', array(
                    'label' => 'Favorite Web Site',
                    'description' => 'Your favorite site to visit on the web.',
                    'belongsTo' => 'user',
                    'maxLength' => 255,
                    'layout' => 'col33',
                )),

                'quote' => array('text', array(
                    'label' => 'Favorite Quote',
                    'description' => 'Words of inspiration or a humorous quote.',
                    'belongsTo' => 'user',
                    'maxLength' => 255,
                    'layout' => 'col33',
                )),

            ),
        ),

        'featured_items' => array(
            'legend' => 'Featured Submission & Profile ID',
            'elements' => array(

                'featured' => array('select', array(
                    'label' => 'Featured Submission',
                    'description' => 'Select your Featured Submission from submissions in your main gallery.',
                    'options' => array('' => 'Disabled'),
                    'belongsTo' => 'user',
                    'layout' => 'col50',
                )),

                'profile_pic' => array('select', array(
                    'label' => 'Profile ID',
                    'description' => 'Select your Profile ID from submissions in your scraps.',
                    'options' => array('' => 'Disabled'),
                    'belongsTo' => 'user',
                    'layout' => 'col50',
                )),

            ),
        ),

        'journal_layout' => array(
            'legend' => 'Journal Layout',
            'elements' => array(

                'journalheader' => array('textarea', array(
                    'label' => 'Journal Header',
                    'description' => 'The following will be displayed at the top of your journal.',
                    'belongsTo' => 'user',
                    'layout' => 'col50',
                    'class' => 'full-width full-height',
                )),

                'journalfooter' => array('textarea', array(
                    'label' => 'Journal Footer',
                    'description' => 'The following will be displayed at the bottom of your journal.',
                    'belongsTo' => 'user',
                    'layout' => 'col50',
                    'class' => 'full-width full-height',
                )),

            ),
        ),

        'contact_social_media' => array(
            'legend' => 'Contact Info - Social Media',
            'elements' => array(),
        ),

        'contact_art_sites' => array(
            'legend' => 'Contact Info - Art Sites',
            'elements' => array(),
        ),

        'contact_instant_messengers' => array(
            'legend' => 'Contact Info - Instant Messengers',
            'elements' => array(),
        ),

        'contact_gaming_services' => array(
            'legend' => 'Contact Info - Gaming Services',
            'elements' => array(),
        ),

        'blocklist_grp' => array(
            'legend' => 'Block List',
            'description' => 'Block users who are being rude or malicious towards you. Blocked users will not be able to comment on your work and journals, shout on your userpage and send you notes.',

            'elements' => array(

                'blocklist' => array('textarea', array(
                    'label' => 'Blocked Users',
                    'description' => '<b>Note:</b> Only enter ONE username per line, otherwise the block feature will not work. Be sure and double check your spelling.',
                    'belongsTo' => 'user',
                    'class' => 'col50',
                )),

            ),
        ),

        'submit' => array(
            'elements' => array(
                // CSRF forgery prevention.
                'csrf' => array('csrf'),

                'submit'        => array('submit', array(
                    'type'  => 'submit',
                    'label' => 'Update Profile',
                    'helper' => 'formButton',
                    'class' => 'btn btn-default',
                )),
            ),
        ),

    ),
);

// Load social information into the appropriate fieldsets.

$social_groups = $config->fa->social->toArray();
foreach($social_groups as $group_key => $group_elements)
{
    $group_name = 'contact_'.$group_key;

    foreach($group_elements as $element_key => $element_info)
    {
        $element_row = array(
            'label'     => $element_info['name'],
            'belongsTo' => 'contact',
            'maxLength' => 255,
            'layout'    => 'col33',
        );

        // Set a description based on the format specified.
        if (!empty($element_info['format']))
        {
            $format_string = str_replace('http://', '', $element_info['format']);
            $element_row['description'] = sprintf($format_string, '<strong>username</strong>');
        }

        $form_config['groups'][$group_name]['elements'][$element_key] = array('text', $element_row);
    }
}

return $form_config;