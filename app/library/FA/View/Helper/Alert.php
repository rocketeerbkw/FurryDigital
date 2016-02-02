<?php
namespace FA\View\Helper;
class Alert extends HelperAbstract
{
    public function alert($message, $level = \FA\Flash::INFO)
    {
        \FA\Flash::addMessage($message, $level);
    }
}