<?php

defined('__DIR__') or define('__DIR__', dirname(__FILE__));
define('DOCUMENT_ROOT', __DIR__);
define('ENV_DATABASE_FILE',
    getenv('ENV_DATABASE_FILE') ?: DOCUMENT_ROOT.'/../../../../config/data/database.db'
);


function tearDown() {
    $dao = new \UrlShortener\Dal\Sqlite\SqlitePDO(ENV_DATABASE_FILE);
    echo " Truncating ...";
    $dao->exec("DELETE FROM shortened_urls_tbl; VACUUM;");
}

function seed() {
    $dao = new \UrlShortener\Dal\Sqlite\SqlitePDO(ENV_DATABASE_FILE);
    $date = "2016-10-13T14:25:32+00:00";
    echo " Truncating ...";
    $dao->exec("INSERT INTO shortened_urls_tbl(id, created_at, updated_at, shortened_url, device_type_redirects_json ) VALUES(
      'http://bbc.com', '$date', '$date', 'aaaaaa', '{}')");

    $dao->exec("INSERT INTO shortened_urls_tbl(id, created_at, updated_at, shortened_url, device_type_redirects_json ) VALUES(
      'http://not-reachable.organ', '$date', '$date', 'bbbbbb', '{}')");

    $dao->exec("INSERT INTO shortened_urls_tbl(id, created_at, updated_at, shortened_url, device_type_redirects_json ) VALUES(
      'http://github.com', '$date', '$date', 'cccccc', '{}')");

    $dao->exec("INSERT INTO shortened_urls_tbl(id, created_at, updated_at, shortened_url, device_type_redirects_json ) VALUES(
      'http://www.iitd.ac.in', '$date', '$date', 'dddddd', '{}')");
    $dao->exec("INSERT INTO shortened_urls_tbl(id, created_at, updated_at, shortened_url, device_type_redirects_json ) VALUES(
      'http://independent.com', '$date', '$date', 'eeeeee', '{\"mobile\": \"http://cnn.com\"}')");
}