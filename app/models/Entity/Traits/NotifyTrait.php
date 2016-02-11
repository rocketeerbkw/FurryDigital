<?php
namespace Entity\Traits;

trait NotifyTrait
{
    public function getIdentifierId()
    {
        return $this->{self::$identifier};
    }

    public function setIdentifierId($new_id)
    {
        return false;
    }

    public static function purgeByIdentifier($user_id, $id)
    {
        $record = self::getRepository()->findOneBy(array(self::$identifier => $id, 'user_id' => $user_id));

        if ($record instanceof self)
            $record->delete();
    }

    public static function purgeAllByUser($user_id)
    {
        $em = self::getEntityManager();
        return $em->createQuery('DELETE FROM '.__CLASS__.' n WHERE n.user_id = :user_id')
            ->setParameter('user_id', $user_id)
            ->execute();
    }
}