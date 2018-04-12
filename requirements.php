<?php
/**
 * Application requirement checker script.
 *
 * In order to run this script use the following console command:
 * php requirements.php
 *
 * In order to run this script from the web, you should copy it to the web root.
 * If you are using Linux you can create a hard link instead, using the following command:
 * ln ../requirements.php requirements.php
 */

// you may need to adjust this path to the correct Yii framework path
// uncomment and adjust the following line if Yii is not located at the default path
//$frameworkPath = dirname(__FILE__) . '/vendor/yiisoft/yii2';


if (!isset($frameworkPath)) {
    $searchPaths = [
        dirname(__FILE__) . '/vendor/yiisoft/yii2',
        dirname(__FILE__) . '/../vendor/yiisoft/yii2',
    ];
    foreach($searchPaths as $path) {
        if (is_dir($path)) {
            $frameworkPath = $path;
            break;
        }
    }
}

if (!isset($frameworkPath) || !is_dir($frameworkPath)) {
    $message = "<h1>Error</h1>\n\n"
        . "<p><strong>The path to yii framework seems to be incorrect.</strong></p>\n"
        . '<p>You need to install Yii framework via composer or adjust the framework path in file <abbr title="' . __FILE__ . '">' . basename(__FILE__) . "</abbr>.</p>\n"
        . '<p>Please refer to the <abbr title="' . dirname(__FILE__) . "/README.md\">README</abbr> on how to install Yii.</p>\n";

    if (!empty($_SERVER['argv'])) {
        // do not print HTML when used in console mode
        echo strip_tags($message);
    } else {
        echo $message;
    }
    exit(1);
}

require_once($frameworkPath . '/requirements/YiiRequirementChecker.php');
$requirementsChecker = new YiiRequirementChecker();

/**
 * Adjust requirements according to your application specifics.
 */
$requirements = array(
    // Database :
    array(
        'name' => 'PDO extension',
        'mandatory' => true,
        'condition' => extension_loaded('pdo'),
        'by' => 'All DB-related classes',
    ),
    array(
        'name' => 'PDO SQLite extension',
        'mandatory' => false,
        'condition' => extension_loaded('pdo_sqlite'),
        'by' => 'All DB-related classes',
        'memo' => 'Required for SQLite database.',
    ),
    array(
        'name' => 'PDO MySQL extension',
        'mandatory' => false,
        'condition' => extension_loaded('pdo_mysql'),
        'by' => 'All DB-related classes',
        'memo' => 'Required for MySQL database.',
    ),
    // PHP ini :
    'phpExposePhp' => array(
        'name' => 'Expose PHP',
        'mandatory' => false,
        'condition' => $requirementsChecker->checkPhpIniOff("expose_php"),
        'by' => 'Security reasons',
        'memo' => '"expose_php" should be disabled at php.ini',
    ),
);

$requirementsChecker->checkYii()->check($requirements)->render();
