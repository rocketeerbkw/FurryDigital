<?php
namespace Modules\Frontend\Controllers;

use Entity\Upload;

class SearchController extends BaseController
{
    public function indexAction()
    {
        $form_config = $this->current_module_config->forms->search->toArray();

        $form_config['elements']['perpage']['default'] = $this->user->getVariable('perpage');

        if (!$this->fa->canSeeArt('mature'))
        {
            unset($form_config['elements']['rating'][1]['options'][Upload::RATING_MATURE]);
            unset($form_config['elements']['rating'][1]['options'][Upload::RATING_ADULT]);
        }
        elseif (!$this->fa->canSeeArt('adult'))
        {
            unset($form_config['elements']['rating'][1]['options'][Upload::RATING_ADULT]);
        }

        $form = new \FA\Form($form_config);

        $data = $form->getDefaults();
        if ($this->request->isPost() && $form->isValid($_POST))
        {
            // Leave all values default if not specified by the user.
            $data = array_merge($data, array_filter($form->getValues()));
        }

        // Force form to default to page 1, so that if filters change, the page will reset.
        $form->populate(array('page' => 1));
        $this->view->form = $form;

        $pager = NULL;

        // Run the search query if the terms were provided
        if (!empty($data['q']))
        {
            // TODO: Add user search back into this.

            // Create the sphinx request hash
            $sphinx_request['q']                = preg_replace('/[\x00-\x1F]/', ' ', $data['q']);
            $sphinx_request['page']             = $data['page'];
            $sphinx_request['perpage']          = $data['perpage'];
            $sphinx_request['use_index']        = 'submissions_delta submissions';
            $sphinx_request['match_mode']       = $this->_sphinx_match_mode($data['mode']);
            $sphinx_request['field_weights']    = array(
                'title'     => 3,
                'keywords'  => 4,
                'message'   => 2,
                'filename'  => 1,
                'lower'     => 1
            );
            $sphinx_request['order_by']        = $data['order_by'];
            $sphinx_request['order_direction'] = $data['order_direction'];

            // Filters
            $sphinx_request['filters'] = array();

            if (isset($data['rating']))
            {
                $sphinx_request['filters']['adultsubmission'] = (array)$data['rating'];

                if (in_array(Upload::RATING_MATURE, $data['rating']) || in_array(Upload::RATING_ADULT, $data['rating']))
                    $this->fa->setPageHasMatureContent(true);
            }

            if (!empty($data['type']))
                $sphinx_request['filters']['type'] = $data['type'];

            if ($data['range'] != 0)
                $sphinx_request['filters']['date'] = array(time()-$data['range'], time());

            // Make the request
            $sphinx_error = NULL;
            $sphinx_warning = NULL;
            $sphinx_result = $this->_sphinx_make_request($sphinx_request, $sphinx_error, $sphinx_warning);

            if (!$sphinx_result)
                throw new \FA\Exception($sphinx_error);

            if ($sphinx_result['total_found'] && isset($sphinx_result['matches']) && is_array($sphinx_result['matches']))
            {
                $found_docs = array();
                $doc_info   = array();

                foreach($sphinx_result['matches'] as $match)
                {
                    $found_docs[] = $match['id'];
                    $doc_info[$match['id']] = $match;
                }

                $result = $this->em->createQuery('SELECT up, us, field(up.id, :ids) AS HIDDEN field FROM Entity\Upload up JOIN up.user us WHERE up.id IN (:ids) ORDER BY field')
                    ->setParameter('ids', $found_docs)
                    ->execute();

                $pager = new \FA\Paginator\Sphinx($result, $sphinx_result['total_found'], $data['page'], $data['perpage']);
            }
        }

        if (!$pager)
            $pager = new \FA\Paginator\Sphinx(array(), 0, $data['page'], $data['perpage']);

        $this->view->page_current = $data['page'];
        $this->view->page_count = $pager->getPageCount();

        $this->view->pager = $pager;
        $this->view->thumbnail_size = $this->user->getVariable('thumbnail_size');
    }

    protected function _sphinx_match_mode($mode)
    {
        switch($mode)
        {
            case MODE_ALL     : return SPH_MATCH_ALL;
            case MODE_ANY     : return SPH_MATCH_ANY;
            case MODE_EXTENDED: // fallthough
            default           : return SPH_MATCH_EXTENDED;
        }
    }

    protected function _sphinx_make_request($_data, &$error, &$warning)
    {
        $sph_config = $this->config->sphinx->toArray();

        // Create a connection
        $sphinx = new \Sphinx\SphinxClient();

        // Setup the query details
        $sphinx->SetServer($sph_config['host'], $sph_config['port']);
        $sphinx->SetMaxQueryTime($sph_config['max_query_time']);
        $sphinx->SetConnectTimeout($sph_config['connection_timeout']);

        $sphinx->SetRankingMode(SPH_RANK_PROXIMITY_BM25);
        $sphinx->SetArrayResult(TRUE);
        $sphinx->SetFieldWeights($_data['field_weights']);
        $sphinx->SetMatchMode($_data['match_mode']);

        foreach($_data['filters'] as $filter_name => $filter_vals) {
            if($filter_name != 'date') {
                $sphinx->SetFilter($filter_name, $filter_vals);
            }
        }

        if(array_key_exists('date', $_data['filters'])) {
            $sphinx->SetFilterRange('date', $_data['filters']['date'][0], $_data['filters']['date'][1]);
        }

        switch($_data['order_by'])
        {
            case 'date':
                $sort_mode = SPH_SORT_EXPR;
                if($_data['order_direction'] == 'asc')
                    $sort_expression = '-date';
                else
                    $sort_expression = 'date';
            break;

            case 'popularity':
                $sort_mode = SPH_SORT_EXPR;

                if($_data['order_direction'] == 'asc')
                    $sort_expression = '-(views*0.5+numtracked*2+comments*4)';
                else
                    $sort_expression = 'views*0.5+numtracked*2+comments*4';
            break;

            case 'relevancy':
            default:
                $sort_mode = SPH_SORT_EXPR;

                if($_data['order_direction'] == 'asc')
                    $sort_expression = '-(@weight + ln(views*0.5+numtracked*4+comments)*100)';
                else
                    $sort_expression = '@weight + ln(views*0.5+numtracked*4+comments)*100';
            break;
        }

        $sphinx->SetSortMode($sort_mode, $sort_expression);
        $sphinx->SetLimits(intval(($_data['page']-1)*$_data['perpage']), intval($_data['perpage']), $sph_config['limits']);

        // Run the query
        $result = $sphinx->Query($_data['q'], $_data['use_index']);

        $error   = $sphinx->GetLastError();
        $warning = $sphinx->GetLastWarning();

        return $result;
    }
}