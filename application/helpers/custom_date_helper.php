<?php
if(!function_exists('system_to_euro_date'))
{
    function system_to_euro_date($givenDate=null)
    {
		$date = new DateTime($givenDate);
        return $date->format('d-m-Y');
    }
}
if(!function_exists('system_to_euro_date_time'))
{
    function system_to_euro_date_time($givenDate=null)
    {
		$date = new DateTime($givenDate);
        return $date->format('d-m-Y H:i:s');
    }
}
if(!function_exists('euro_to_system_date_time'))
{
    function euro_to_system_date_time($givenDate=null)
    {
		$date = new DateTime($givenDate);
        return $date->format('Y-m-d H:i:s');
    }
}