<?php
namespace FA\View\Helper;
class Truncate extends HelperAbstract
{
    public function truncate($text, $length=80)
    {
        return \FA\Utilities::truncateText($text, $length);
    }
}