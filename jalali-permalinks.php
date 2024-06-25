<?php

/**
 * Plugin Name: Jalali Permalinks
 * Description: Enables the Shamsi month name tag for permalinks.
 * Author: Mohammad Reza Tayyebi <github@tyyi.net>
 * License: GPLv2
 */

// example: /%jyear%-%jmonthname%/%postname%/

define('MY_PLUGIN_DIR', plugin_dir_path(__FILE__));
include MY_PLUGIN_DIR . 'parsidate.php';

class JalaliPermalinks {
    public static $monthnames = array(
        'farvardin', 'ordibehesht', 'khordad', 'tir', 'mordad', 'shahrivar',
        'mehr', 'aban', 'azar', 'dey', 'bahman', 'esfand'
    );

    public static function init() {
        add_rewrite_tag('%jmonthname%', '(' . implode('|', self::$monthnames) . ')');
        add_rewrite_rule('^([0-9]{4})-(' . implode('|', self::$monthnames) . ')/([^/]+)/?', 'index.php?name=$matches[3]', 'top');
    }

    public static function filter_post_link($permalink, $post) {
//        if (false === strpos($permalink, '%j')) {
//            return $permalink;
//        }

	$gregorian_date = get_the_date('Y-m-d', $post);
	$parsidate = new bn_parsidate();
	$jmonthname_persian = strtolower($parsidate->persian_date('F', $gregorian_date, 'per'));
	$jyear = $parsidate->persian_date('Y', $gregorian_date, 'en');

        $monthindex = intval(get_post_time('n', false, $post->ID));
	$jmonthindex = $parsidate->persian_date('m', $gregorian_date, 'en');
        $monthname = self::$monthnames[intval($jmonthindex) - 1];
        $permalink = str_replace('%jmonthname%', $monthname, $permalink);
	$permalink = str_replace('%jyear%', $jyear, $permalink);

        return $permalink;
    }
}

add_action('init', array('JalaliPermalinks', 'init'));
