<?php

/**
 * Plugin version and other meta-data are defined here.
 *
 * @package     local_translate
 * @copyright   2020 Igor <sat.igor.khilman@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$plugin->component = 'local_translate';
$plugin->release = '0.1.1';
$plugin->version = 2020021702;
$plugin->requires = 2019052000;
$plugin->maturity = MATURITY_ALPHA;
$plugin->dependencies = array(
    'filter_multilang2' => ANY_VERSION,
);