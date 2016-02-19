<?php
namespace App;

class Notifications
{
    /**
     * Dispatch a notification, and increment user notification counts.
     *
     * @param $entity_name
     * @param $entity_id
     * @param null $user_id
     * @throws \App\Exception
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

                // Remove all notifications relevant to entity.
                // $remove_notification_query = $em->createQuery('DELETE FROM '.$einfo['notify_table'].' nt WHERE nt.user_id=:user_id nt.'.$einfo['relationship'].'_id = :entity_id');

                // Prepare query for notification entries.
                foreach($watching_users as $row)
                {
                    /*
                    $remove_notification_query->setParameters(array(
                        'entity_id' => $source_id,
                        'user_id'   => $row['user_id'],
                    ))->execute();
                    */

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
     * @throws \App\Exception
     */
    public static function purge($entity_name, $entity_id, $user_id = NULL)
    {
        $em = self::getEntityManager();
        $einfo = self::getEntityInfo($entity_name);

        // Remove all notifications relevant to entity.
        $remove_notification_query = $em->createQuery('DELETE FROM '.$einfo['notify_table'].' nt WHERE nt.'.$einfo['relationship'].'_id = :entity_id');

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
                $user_ids_raw = $em->createQuery('SELECT nt.user_id FROM '.$einfo['notify_table'].' nt WHERE nt.'.$einfo['relationship'].'_id = :entity_id')
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
        $di = \Phalcon\Di::getDefault();
        $config = $di->get('config');

        $entity_info = $config->fd->notifications->toArray();

        if (!isset($entity_info[$entity_name]))
            throw new \App\Exception('Cannot handle notifications for "'.$entity_name.'".');

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