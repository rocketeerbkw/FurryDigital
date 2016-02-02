<?php
/**
 * Submission Types and specifications for each.
 */

use Entity\Upload;

return array(
    Upload::TYPE_IMAGE => array(
        'name'          => 'Artwork / Image',
        'description'   => 'Drawn or rendered artwork, illustrations and sketches.',
        'extensions'    => 'JPG, GIF, PNG',
        'types'         => array('image/jpeg', 'image/gif', 'image/png'),
        'folder'        => 'images',
    ),
    Upload::TYPE_TEXT  => array(
        'name'          => 'Story / Poetry / Text',
        'description'   => 'Short stories, epic tales, poetry, prose, and other fanciful forms of creative writing.',
        'extensions'    => 'DOC, DOCX, RTF, TXT, PDF, ODT',
        'types'         => array('application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/rtf', 'text/richtext', 'text/plain', 'application/pdf', ''),
        'category'      => 25,
        'folder'        => 'text',
    ),
    Upload::TYPE_AUDIO => array(
        'name'          => 'Music / Audio',
        'description'   => 'Music, audio logs, podcasts, rants and more.',
        'extensions'    => 'MP3, WAV, MID',
        'types'         => array('audio/mpeg', 'audio/midi', 'audio/x-wav'),
        'category'      => 16,
        'folder'        => 'audio',
    ),
    Upload::TYPE_VIDEO => array(
        'name'          => 'Flash',
        'description'   => 'Flash animation and interactives.',
        'extensions'    => 'SWF',
        'types'         => array('application/x-shockwave-flash'),
        'category'      => 7,
        'folder'        => 'video',
    ),
);