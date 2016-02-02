<?php
namespace App\Forms\Element;

class Image extends File
{
    public function renderView()
    {
        if (empty($this->_previous_value))
            return '';

        $file_rows = array();
        $i = 1;
        $di = \Phalcon\Di::getDefault();

        foreach((array)$this->_previous_value as $file)
        {
            $file_url = $di['url']->upload($file);
            $file_rows[] = '<div><img class="thumbnail" src="'.$file_url.'" alt="Image #'.$i.'" style="max-width: 300px;"></div>';

            $i++;
        }

        return implode('', $file_rows);
    }
}