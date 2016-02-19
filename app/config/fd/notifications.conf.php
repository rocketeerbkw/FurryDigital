<?php
/**
 * Notification Information
 *
 * Legend:
 * 'notify_key' => array(
 *      'relation', <- The name of the relationship in the User entity (i.e. $user->x_notifications becomes 'x_notifications').
 *      'abbr', <- The one or two-letter abbreviation to use in the site header.
 *      'title', <- The visible name for the notification type.
 *      'type', <- The page used to show this notification type (most are shown in "other")
 *
 *      'single_user', <- TRUE if only one user gets notified at a time, FALSE if all watching users get notified.
 *      'entity_table', <- The class of the table being notified for
 *      'notify_table', <- The class of the notification table for this type (if it exists)
 *      'user_count', <- The property on the User object that stores a cached count of notifications for this type
 *      'relationship', <- The relationship 'notify_table' uses to refer to 'entity_table'
 *      'secondary', <- If 'notify_table' requires a secondary details field, this is the name of that field
 *  );
 */

return array(
    'favorite' => array(
        'relation'          => 'favorite_notifications',
        'abbr'              => 'F',
        'title'             => 'Favorite',
        'type'              => 'other',

        'single_user'       => true,
        'entity_table'      => 'Entity\Favorite',
        'notify_table'      => 'Entity\FavoriteNotify',
        'user_count'        => 'notify_favorites',
        'relationship'      => 'favorite',
        'secondary'         => 'upload_id',
    ),
    'upload' => array(
        'relation'          => 'upload_notifications',
        'abbr'              => 'S',
        'title'             => 'Upload',
        'type'              => 'uploads',

        'single_user'       => false,
        'entity_table'      => 'Entity\Upload',
        'notify_table'      => 'Entity\UploadNotify',
        'user_count'        => 'notify_uploads',
        'relationship'      => 'upload',
    ),
    'upload_comment' => array(
        'relation'          => 'upload_comment_notifications',
        'abbr'              => 'C',
        'title'             => 'Comment',
        'type'              => 'other',

        'single_user'       => true,
        'entity_table'      => 'Entity\UploadComment',
        'notify_table'      => 'Entity\UploadCommentNotify',
        'user_count'        => 'notify_upload_comments',
        'relationship'      => 'comment',
        'secondary'         => 'upload_id',
    ),
    'journal' => array(
        'relation'          => 'journal_notifications',
        'abbr'              => 'J',
        'title'             => 'Journal',
        'type'              => 'other',

        'single_user'       => false,
        'entity_table'      => 'Entity\Journal',
        'notify_table'      => 'Entity\JournalNotify',
        'user_count'        => 'notify_journals',
        'relationship'      => 'upload',
    ),
    'journal_comment' => array(
        'single_user'       => true,
        'entity_table'      => 'Entity\JournalComment',
        'notify_table'      => 'Entity\JournalCommentNotify',
        'user_count'        => 'notify_comments',
        'relationship'      => 'comment',
    ),
    'note' => array(
        'relation'          => 'notes_received',
        'abbr'              => 'N',
        'title'             => 'Note',
        'type'              => 'pms',

        'single_user'       => true,
        'entity_table'      => 'Entity\Note',
        'user_count'        => 'notify_notes',
    ),
    'shout' => array(
        'relation'          => 'shout_notifications',
        'abbr'              => 'SH',
        'title'             => 'Shout',
        'type'              => 'other',

        'single_user'       => true,
        'entity_table'      => 'Entity\Shout',
        'notify_table'      => 'Entity\ShoutNotify',
        'user_count'        => 'notify_shouts',
        'relationship'      => 'shout',
        'secondary'         => 'source_id',
    ),
    'trouble_ticket' => array(
        'relation'          => 'trouble_ticket_notifications',
        'abbr'              => 'TT',
        'title'             => 'Trouble Ticket',
        'type'              => 'other',

        'single_user'       => true,
        'entity_table'      => 'Entity\TroubleTicket',
        'notify_table'      => 'Entity\TroubleTicketNotify',
        'user_count'        => 'notify_tickets',
        'relationship'      => 'ticket',
    ),
    'watch' => array(
        'relation'          => 'watch_notifications',
        'abbr'              => 'W',
        'title'             => 'Watch',
        'type'              => 'other',

        'single_user'       => true,
        'entity_table'      => 'Entity\Watch',
        'notify_table'      => 'Entity\WatchNotify',
        'user_count'        => 'notify_watches',
        'relationship'      => 'watch',
        'secondary'         => 'source_id',
    ),
);