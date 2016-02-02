<?php
namespace FA\View\Helper;
class Money extends HelperAbstract
{
    public function money($amount)
    {
        return \FA\Utilities::money_format($amount);
    }
}