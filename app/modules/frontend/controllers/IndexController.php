<?php
namespace Modules\Frontend\Controllers;

use Entity\Upload;
use Entity\User;

class IndexController extends BaseController
{
    public function indexAction()
    {
        $cache = $this->di->get('cache');

        $rating_query = '(true = true)';
        $rating_cache = '';

        if ($this->fa->canSeeArt('adult'))
        {
            $rating_cache = 'gma';
        }
        elseif ($this->fa->canSeeArt('mature'))
        {
            $rating_query = '(up.rating = '.Upload::RATING_GENERAL.' OR up.rating = '.Upload::RATING_MATURE.')';
            $rating_cache = 'gm-';
        }
        else
        {
            $rating_query = '(up.rating = '.Upload::RATING_GENERAL.')';
            $rating_cache = 'g--';
        }

        $frontpage_cache_key = 'fa.frontpage.recent'.$rating_cache;
        $frontpage_cache_lifetime = 30; // 30 seconds
        $frontpage_data = $cache->load($frontpage_cache_key);

        if (!$frontpage_data)
        {
            $frontpage_data = array();

            $record_types = array(
                'images'    => Upload::TYPE_IMAGE,
                'audio'     => Upload::TYPE_AUDIO,
                'text'      => Upload::TYPE_TEXT,
            );
            $type_records_query = $this->em->createQuery('SELECT up.id, up.title, up.description, up.rating, up.thumbnail, us.username, us.lower, us.avatar_mtime FROM Entity\Upload up JOIN up.user us WHERE up.upload_type = :type AND up.is_scrap = 0 AND '.$rating_query.' ORDER BY up.id DESC')->setMaxResults(21);

            foreach($record_types as $type_key => $type_code)
            {
                $type_records_raw = $type_records_query->setParameter('type', $type_code)->getArrayResult();
                $type_records = array();

                foreach($type_records_raw as $record)
                {
                    if ($record['rating'] != Upload::RATING_GENERAL)
                        $this->fa->setPageHasMatureContent(true);

                    $record['rating_text'] = Upload::getRatingText($record['rating']);
                    $record['thumbnail_url'] = Upload::getFileUrl($record['thumbnail']);

                    $record['avatar'] = User::getUserAvatar($record['lower'], $record['avatar_mtime']);

                    $type_records[$record['id']] = $record;
                }

                $frontpage_data[$type_key] = $type_records;
            }

            $cache->set($frontpage_data, $frontpage_cache_key, $frontpage_cache_lifetime);
        }

        $this->view->records = $frontpage_data;
    }
}