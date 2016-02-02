<?php
namespace FA\Legacy;

class Notifications
{
    /**
     * Dispatch a notification, and increment user notification counts.
     *
     * @param $entity_name
     * @param $entity_id
     * @param null $user_id
     * @throws \FA\Exception
     */
    public static function dispatch($entity_name, $source_id, $user_id = NULL, $secondary_id = NULL)
    {
        $em = self::getEntityManager();
        $einfo = self::getEntityInfo($entity_name);

        $update_counts_query = $em->createQuery('UPDATE Entity\User u SET u.'.$einfo['user_count'].'=u.'.$einfo['user_count'].'+1 WHERE u.id = :user_id');

        if (!empty($einfo['notify_table']))
        {
            if ($einfo['single_user'])
            {
                // Create new notification.
                $notify_record = new $einfo['notify_table'];

                $record_info = array(
                    'user_id' => $user_id,
                    $einfo['relationship'].'_id' => $source_id,
                );
                if (isset($einfo['secondary']) && $secondary_id)
                    $record_info[$einfo['secondary']] = $secondary_id;

                $notify_record->fromArray($record_info);
                $notify_record->save();

                // Increment user count.
                $update_counts_query->setParameter('user_id', $user_id)
                    ->execute();
            }
            else
            {
                // Get all users that are watching the originating user.
                $watching_users = $em->createQuery('SELECT w.user_id, w.watch_type FROM Entity\Watch w WHERE w.target_id = :user_id')
                    ->setParameter('user_id', $user_id)
                    ->getArrayResult();

                // Prepare query for notification entries.
                $notify_table_query = $em->createQuery('INSERT INTO ' . $einfo['notify_table'] . ' nt SET nt.user_id=:user_id, nt.' . $einfo['relationship'] . '_id=:entity_id, nt.source_id=:source_id');

                foreach($watching_users as $row)
                {
                    // Create new notification.
                    $notify_record = new $einfo['notify_table'];

                    $record_info = array(
                        'user_id' => $row['user_id'],
                        $einfo['relationship'].'_id' => $source_id,
                    );
                    if (isset($einfo['secondary']) && $secondary_id)
                        $record_info[$einfo['secondary']] = $secondary_id;

                    $notify_record->fromArray($record_info);
                    $em->persist($notify_record);

                    // Update user counts.
                    $update_counts_query->setParameter('user_id', $row['user_id'])
                        ->execute();
                }

                $em->flush();
            }
        }
        else
        {
            // Just increment the user counter.
            $update_counts_query->setParameter('user_id', $user_id)
                ->execute();
        }
    }

    /**
     * Remove notifications related to a given entity type, and
     * lower the counts for users who were notified about the entity.
     *
     * @param $entity_name
     * @param $entity_id
     * @param null $user_id
     * @throws \FA\Exception
     */
    public static function purge($entity_name, $entity_id, $user_id = NULL)
    {
        $em = self::getEntityManager();
        $einfo = self::getEntityInfo($entity_name);

        // Remove all notifications relevant to entity.
        $remove_notification_query = $em->createQuery('DELETE FROM '.$einfo['notify_table'].' nt WHERE nt.'.$einfo['relationship'].' = :entity_id');

        // Lower count of notifications for any affected users.
        $lower_counts_query = $em->createQuery('UPDATE Entity\User u SET u.'.$einfo['user_count'].'=IF(u.'.$einfo['user_count'].'>0, u.'.$einfo['user_count'].'-1, 0) WHERE u.id = :user_id');

        if (!empty($einfo['notify_table']))
        {
            if ($einfo['single_user'])
            {
                $lower_counts_query->setParameter('user_id', $user_id)
                    ->execute();

                // Remove all notifications relevant to entity.
                $remove_notification_query->setParameter('entity_id', (int)$entity_id)
                    ->execute();
            }
            else
            {
                $user_ids_raw = $em->createQuery('SELECT nt.user_id FROM '.$einfo['notify_table'].' nt WHERE nt.'.$einfo['relationship'].' = :entity_id')
                    ->setParameter('entity_id', (int)$entity_id)
                    ->getArrayResult();

                foreach($user_ids_raw as $row)
                {
                    $lower_counts_query->setParameter('user_id', $row['user_id'])
                        ->execute();
                }

                // Remove all notifications relevant to entity.
                $remove_notification_query->setParameter('entity_id', (int)$entity_id)
                    ->execute();
            }
        }
        else
        {
            $lower_counts_query->setParameter('user_id', $user_id)
                ->execute();
        }
    }

    protected static function getEntityInfo($entity_name)
    {
        $entity_info = array(
            'favorite' => array(
                'single_user'       => true,
                'entity_table'      => 'Entity\Favorite',
                'notify_table'      => 'Entity\FavoriteNotify',
                'user_count'        => 'notify_favorites',
                'relationship'      => 'favorite',
                'secondary'         => 'upload_id',
            ),
            'upload' => array(
                'single_user'       => false,
                'entity_table'      => 'Entity\Upload',
                'notify_table'      => 'Entity\UploadNotify',
                'user_count'        => 'notify_uploads',
                'relationship'      => 'upload',
            ),
            'upload_comment' => array(
                'single_user'       => true,
                'entity_table'      => 'Entity\UploadComment',
                'notify_table'      => 'Entity\UploadCommentNotify',
                'user_count'        => 'notify_comments',
                'relationship'      => 'comment',
                'secondary'         => 'upload_id',
            ),
            'journal' => array(
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
                'single_user'       => true,
                'entity_table'      => 'Entity\Note',
                'user_count'        => 'notify_notes',
            ),
            'shout' => array(
                'single_user'       => true,
                'entity_table'      => 'Entity\Shout',
                'notify_table'      => 'Entity\ShoutNotify',
                'user_count'        => 'notify_shouts',
                'relationship'      => 'shout',
                'secondary'         => 'source_id',
            ),
            'ticket' => array(
                'single_user'       => true,
                'entity_table'      => 'Entity\TroubleTicket',
                'notify_table'      => 'Entity\TroubleTicketNotify',
                'user_count'        => 'notify_tickets',
                'relationship'      => 'ticket',
            ),
            'watch' => array(
                'single_user'       => true,
                'entity_table'      => 'Entity\Watch',
                'notify_table'      => 'Entity\WatchNotify',
                'user_count'        => 'notify_watches',
                'relationship'      => 'watch',
                'secondary'         => 'source_id',
            ),
        );

        if (!isset($entity_info[$entity_name]))
            throw new \FA\Exception('Cannot handle notifications for "'.$entity_name.'".');

        return $entity_info[$entity_name];
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    protected static function getEntityManager()
    {
        $di = \Phalcon\Di::getDefault();
        return $di['em'];
    }
}