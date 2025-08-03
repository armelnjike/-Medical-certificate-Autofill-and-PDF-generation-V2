<?php

declare(strict_types=1);

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Configuration array
$config = [
    'db' => [
        'host' => 'localhost',
        'name' => 'login_system',
        'user' => 'root',
        'pass' => 'armel123'
    ]
];

/**
 * Get database connection
 * @return mysqli Database connection
 */
function getDbConnection(): mysqli
{
    global $config;
    $con = new mysqli(
        $config['db']['host'],
        $config['db']['user'],
        $config['db']['pass'],
        $config['db']['name']
    );

    if ($con->connect_error) {
        die('Connection failed: ' . $con->connect_error);
    }

    $con->set_charset('utf8');
    return $con;
}

?>