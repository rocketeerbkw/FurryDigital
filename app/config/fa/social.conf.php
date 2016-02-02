<?php
/**
 * An array of social types, plus additional information about each type.
 *
 * group_name => array(
 *   key_name => array(
 *     'name',   // The label for form fields
 *     'format', // The special sprintf() formatting rule to apply to this field, if any
 *               // for example, youtube.com/%s will print youtube.com/{{ youtube }}
 *   ),
 * ),
 *
 * Groups are separated into patterns of 3 sites, as this is how they are displayed.
 */

return array(

    'social_media' => array(

        'website' => array(
            'name'      => 'Personal Website Address',
            'format'    => '',
        ),
        'youtube' => array(
            'name'      => 'YouTube',
            'format'    => 'http://youtube.com/user/%s',
        ),
        'twitter' => array(
            'name'      => 'Twitter',
            'format'    => 'http://twitter.com/%s',
        ),

        'livestream' => array(
            'name'      => 'LiveStream',
            'group'     => 'social_media',
            'format'    => 'http://livestream.com/%s',
        ),
        'ustream' => array(
            'name'      => 'UStream',
            'format'    => 'http://ustream.tv/user/%s',
        ),
        'livejournal' => array(
            'name'      => 'LiveJournal',
            'format'    => 'http://%s.livejournal.com',
        ),

        'facebook' => array(
            'name'      => 'Facebook',
            'format'    => 'http://facebook.com/%s',
        ),
        'furbuy' => array(
            'name'      => 'Furbuy',
            'format'    => 'http://furbuy.com/seller/%s.html',
        ),
        'dealersden' => array(
            'name'      => 'Dealer\'s Den',
            'format'    => '',
        ),

        'patreon' => array(
            'name'      => 'Patreon',
            'format'    => 'http://patreon.com/%s',
        ),
        'etsy' => array(
            'name'      => 'Etsy',
            'format'    => 'http://etsy.com/shop/%s',
        ),

    ),

    'art_sites' => array(

        'sofurry' => array(
            'name'      => 'SoFurry',
            'format'    => 'http://%s.sofurry.com',
        ),
        'inkbunny' => array(
            'name'      => 'InkBunny',
            'format'    => 'http://inkbunny.net/%s',
        ),
        'deviantart' => array(
            'name'      => 'DeviantArt',
            'format'    => 'http://%s.deviantart.com',
        ),

        'nabyn' => array(
            'name'      => 'Nabyn',
            'format'    => 'http://%s.nabyn.com',
        ),
        'transfur' => array(
            'name'      => 'Transfur',
            'format'    => 'http://transfur.com/Users/%s',
        ),
        'tumblr' => array(
            'name'      => 'Tumblr',
            'format'    => 'http://%s.tumblr.com',
        ),

        'weasyl' => array(
            'name'      => 'Weasyl',
            'format'    => 'http://weasyl.com/%s',
        ),

    ),

    'instant_messengers' => array(

        'aim' => array(
            'name'      => 'AOL Instant Messenger',
        ),
        'yim' => array(
            'name'      => 'Yahoo! Instant Messenger',
        ),
        'icq' => array(
            'name'      => 'ICQ',
        ),

        'skype' => array(
            'name'      => 'Skype',
        ),
        'jabber' => array(
            'name'      => 'Jabber/Google Talk',
        ),

    ),

    'gaming_services' => array(

        'steam' => array(
            'name'      => 'Steam',
            'format'    => 'http://steamcommunity.com/id/%s',
        ),
        'xbl' => array(
            'name'      => 'XBox Live',
        ),
        'secondlife' => array(
            'name'      => 'Second Life',
        ),

        'imvu' => array(
            'name'      => 'IMVU',
        ),
        'psn' => array(
            'name'      => 'PSN',
        ),
        'xfire' => array(
            'name'      => 'XFire',
        ),

        'three_ds' => array(
            'name'      => '3DS ID',
        ),
        'nintendo' => array(
            'name'      => 'Nintendo ID',
        ),
        'raptr' => array(
            'name'      => 'Raptr',
        ),

    ),
);