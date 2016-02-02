<?php
/**
 * FloofClub Legacy Classes
 * Imported from older application codebase.
 */

namespace App\Legacy;

class Utilities
{
    const UUID_REGEX = '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/';

    /**
     * Unique userID generator
     * Conforms to RFC4122
     * http://www.ietf.org/rfc/rfc4122.txt
     *
     * @return string
     */
    public static function uuid()
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    /**
     * Generate random string of desired length
     * Could be a bit more streamlined and effiecient though, but this will work just as well for now.
     *
     * @param $length int
     * @return string
     */
    public static function gen_random_string($length)
    {
        $chars = array(
            'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm',
            'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z',
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M',
            'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
            '0', '1', '2', '3', '4', '5', '6', '7', '8', '9'
        );

        $len = count($chars);
        $result = '';

        for ($i = 0; $i < $length; $i++)
        {
            $rand = mt_rand(0, $len - 1);
            $result .= $chars[$rand];
        }

        return $result;
    }

    /**
     * Recursively reverse an array that has numeric values and text keys.
     *
     * @param $source
     * @return array
     */
    public static function reverseArray($source)
    {
        $result = array();

        foreach((array)$source as $source_key => $source_val)
        {
            if (is_array($source_val))
                $result[$source_key] = self::reverseArray($source_val);
            else
                $result[$source_val] = $source_key;
        }

        return $result;
    }

    /**
     * Wrapper for the "setcookie" function.
     *
     * @param $name
     * @param $value
     * @param bool|FALSE $expires
     * @param null $path
     * @param null $domain
     * @param bool|false $secure
     */
    public static function fa_setcookie($name, $value, $expires = FALSE, $path = null, $domain = null, $secure = false)
    {
        setcookie(
            $name,
            $value,
            ($expires !== FALSE ? $expires : time() + 60 * 60 * 24 * 30),
            ($path !== null ? $path : '/'),
            ($domain !== null ? $domain : '.floof.club'),
            $secure
        );
    }
    
    /**
     * Calculating comments indentation.
     *
     * @param int $level
     * @return string $secure
     */
    public static function levpercent($level)
    {
        //  Table width = 100% - the level * 5%...
        $levwidth = 100 - ($level*3);

        if($level > 20)
            $levwidth = 40;

        return $levwidth.'%';
    }

    /**
     * "Fuzzy Date":
     * Function to display the difference between $time1 and $time2,
     * or current time, if the latter was not specified in human readable
     * format, with $date_format as a description popup.
     *
     * @param $time1
     * @param bool|false $time2
     * @param string $date_format
     * @param string $prefix
     * @param string $suffix
     * @param string $raw
     * @return string
     */
    public static function age($time1, $time2=FALSE, $date_format='', $prefix='', $suffix='', $raw='')
    {
        if(!$date_format)
            $date_format = 'F j, Y, H:i:s';

        ////  Measure scales
        //  seconds
        //  minutes
        //  hours
        //  days
        //  months
        //  years

        // minute = 60
        // hour   = 60*60
        // day    = 60*60*24
        // month  = 60*60*24*30
        // year   = 60*60*24*365

        //                                           //
        ////*******  V A L I D A T I O N  *********////
        //                                           //

        //  If there were no parameters specified, make zero time difference
        if($time1 == FALSE && $time2 == FALSE)
            $time1 = $time2 = time();

        // If there was only one time specified, use current time as the second parameter
        if(!$time2)
            $time2 = time();

        // Make sure time1 is less than time2
        if($time1 > $time2)
        {
            $tmp   = $time1;
            $time1 = $time2;
            $time2 = $tmp;
        }

        $base_time = $time1;
        $new_time  = $time2;


        $diff      = $new_time - $base_time;
        $years     = (int)($diff / 31536000);      // 60*60*24*365

        $diff_temp = $diff % 31536000;             // 60*60*24*365
        $months    = (int)($diff_temp / 2592000);  // 60*60*24*31

        $diff_temp = $diff % 2592000;              // 60*60*24*31
        $days      = (int)($diff_temp / 86400);    // 60*60*24

        $diff_temp = $diff % 86400;                // 60*60*24
        $hours     = (int)($diff_temp / 3600);     // 60*60

        $diff_temp = $diff % 3600;                 // 60*60
        $minutes   = (int)($diff_temp / 60);       // 60

        $diff_temp = $diff % 60;                   // 60*60*24*365
        $seconds   = (int)($diff_temp % 60);       // 1

        if($seconds > 45)
            $minutes++;

        if($minutes > 45)
            $hours++;

        if($hours > 18)
            $days++;

        if($days > 23)
            $months++;

        if($months > 10) {
            $years++;
        }

        if($minutes > 60) {
            $hours++;
            $minutes -= 60;
        }

        if($hours > 24) {
            $days++;
            $hours -= 24;
        }

        if($days > 30) {
            $months++;
            $days -= 30;
        }

        if($months > 12) {
            $years++;
            $months -= 12;
        }

        // Now
        if($years == 0 && $months == 0 && $days == 0 && $hours == 0 && $minutes == 0 && $seconds == 0)
            $result = 'less than a second';

        //                                          //
        ////**************  Seconds  *************////
        //                                          //
        // a second      a second
        // couple        2-4
        // a few         5-10
        // some          10-30
        // half-o-minute 30-44
        // a minute      > 45
        //
        if($years == 0 && $months == 0 && $days == 0 && $hours == 0 && $minutes == 0 && $seconds != 0)
        {
            if($seconds == 1)
                $result = 'a second';
            elseif($seconds < 5)
                $result = 'couple of seconds';
            elseif($seconds < 11)
                $result = 'a few seconds';
            elseif($seconds < 31)
                $result = 'several seconds';
            elseif($seconds < 45)
                $result = 'half a minute';
            else
                $result = 'a minute';
        }

        //                                           //
        ////***************  Minutes  *************////
        //                                           //
        // a  minute     a minute
        // couple        2-4
        // a few         5-10
        // 15           10-20
        // half-an-hour  20-40
        // an hour       > 45
        //
        if($years == 0 && $months == 0 && $days == 0 && $hours == 0 && $minutes != 0)
        {
            if($minutes == 1)
                $result = 'a minute';
            elseif ($minutes < 5)
                $result = 'couple of minutes';
            elseif ($minutes < 11)
                $result = 'a few minutes';
            elseif ($minutes < 21)
                $result = '15 minutes';
            elseif ($minutes < 41)
                $result = 'half-an-hour';
            else
                $result = 'an hour';
        }



        //                                      //
        ////*************  Hours  ************////
        //                                      //
        // an hour     an hour
        // $hours      2-18
        // a day       > 19
        //
        if($years == 0 && $months == 0 && $days == 0 && $hours != 0)
        {
            if($hours == 1)
                $result = 'an hour';
            elseif ($hours < 19)
                $result = $hours.' hours';
            else
                $result = 'a day';
        }


        //                                      //
        ////*************  Days  *************////
        //                                      //
        // a  day        a day
        // $number       2-5
        // a week        6-8
        // $number       9-12
        // 2 weeks       13-15
        // $number       16-19
        // 3 weeks       20-22
        // $number       23-25
        // a month       > 26
        //
        if($years == 0 && $months == 0 && $days != 0)
        {
            if($days == 1)
                $result = 'a day';
            elseif ($days < 6)
                $result = $days.' days';
            elseif ($days < 9)
                $result = 'a week';
            elseif ($days < 13)
                $result = $days.' days';
            elseif ($days < 16)
                $result = '2 weeks';
            elseif ($days < 20)
                $result = $days.' days';
            elseif ($days < 23)
                $result = '3 weeks';
            elseif ($days < 26)
                $result = $days.' days';
            else
                $result = 'a month';
        }


        //                                        //
        ////*************  Months  *************////
        //                                        //
        // a month       a month
        // $number       2-10
        // a year        > 11
        //
        if($years == 0 && $months != 0)
        {
            if ($months == 1)
                $result = 'a month';
            elseif ($months < 11)
                $result = $months.' months';
            else
                $result = 'a year';
        }



        //                                        //
        ////*************  Years  *************////
        //                                        //
        // a year       a year
        // $number       2-10
        //
        if($years != 0)
        {
            if($years == 1)
                $result = 'a year';
            else
                $result = $years.' years';
        }

        $date_format = 'F j, Y, H:i';
        $base_date   = @date($date_format, $base_time);

        if(!$raw) {
            $return = '<span class="popup_date" title="'.$base_date.'">'.$prefix.$result.$suffix.'</span>';
        } else {
            $return = $prefix.$result.$suffix;
        }

        return $return;
    }
}