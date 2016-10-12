<?php
/**
 * Created by PhpStorm.
 * User: nelly
 * Date: 10/10/16
 * Time: 5:28 PM
 */

namespace UrlShortener\Dal\Sqlite;


use UrlShortener\Dal\UrlShortenerDao;
use UrlShortener\Models\UrlShortener;
use DateTime;

class RelationalUrlShortenerDao implements UrlShortenerDao
{

    /**
     * @var SqlitePDO
     */
    private $pdo;
    const TABLE = "shortened_urls_tbl";
    private $serviceAddress;
    /**
     * RelationalUrlShortenerDao constructor.
     * @param SqlitePDO $pdo
     */
    public function __construct(SqlitePDO $pdo, string $seviceAddress)
    {
        $this->pdo = $pdo;
        $this->serviceAddress = $seviceAddress;
    }

    public function create(UrlShortener $model) :int
    {
        $id = $model->getId();
        $shortenedUrl = $model->getShortenedUrl();
        $createdAt = $model->getCreatedAtAsString();
        $updatedAt = $model->getUpdatedAtAsString();
        $redirects = $model->getDeviceRedirectsJson();

        $query = "INSERT INTO shortened_urls_tbl(id, shortened_url, created_at, updated_at, device_type_redirects_json) 
              VALUES('$id', '$shortenedUrl', '$createdAt', '$updatedAt', '$redirects'
              )
          ";
        return $this->pdo->exec($query);
    }

    public function update(UrlShortener $model) :int
    {
        $table = self::TABLE;
        $id = $model->getId();
        $updatedAt = (new DateTime())->format("c");
        $redirects = $model->getDeviceRedirectsJson();

        $query = "UPDATE $table SET   
              updated_at='$updatedAt',
              device_type_redirects_json ='$redirects'
              WHERE id ='$id'
          ";
        return $this->pdo->exec($query);
    }

    public function findById(string $id) :UrlShortener
    {
        $table = self::TABLE;
        $query = "SELECT device_type_redirects_json as redirectsJson,id, shortened_url as shortenedUrl, created_at as createdAt, updated_at as updatedAt 
        FROM `$table` 
        WHERE id =:id";
        $params = [":id" =>$id];
        return $this->pdo->fetchObject($query, $this->rowMapper(), $params);
    }


    public function findByShortenedUrl(string $shortenedUrl) :UrlShortener
    {
        $table = self::TABLE;
        $query = "SELECT device_type_redirects_json as redirectsJson,id, shortened_url as shortenedUrl, created_at as createdAt, updated_at as updatedAt 
        FROM `$table` 
        WHERE shortened_url =:sUrl";
        $params = [":sUrl" =>$shortenedUrl];
        return $this->pdo->fetchObject($query, $this->rowMapper(), $params);
    }

    /**
     * @param string $shortenedUrl
     * @return UrlShortener[]
     */
    public function findAll() :array
    {
        $table = self::TABLE;
        $query = "SELECT device_type_redirects_json as redirectsJson,id, shortened_url as shortenedUrl, created_at as createdAt, updated_at as updatedAt 
        FROM `$table`";

        return $this->pdo->fetchList($query, $this->rowMapper());
    }

    private function rowMapper() :callable {
        return function(\stdClass $obj) {
            return new UrlShortener(
                $createdAt = new DateTime($obj->createdAt),
                $updatedAt = new DateTime($obj->updatedAt),
                $shortenedUrl = $obj->shortenedUrl,
                $id = $obj->id,
                $redirectsJson = $obj->redirectsJson,
                $baseUrl = $this->serviceAddress
            );
        };
    }

    public function delete(string $id) :int
    {
        $table = self::TABLE;
        $query = "DELETE FROM $table  WHERE id ='$id' ";
        return $this->pdo->exec($query);
    }
}