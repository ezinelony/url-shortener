<?php

namespace UrlShortener\Dal\Sqlite;

use PDO;
use PDOStatement;
use UrlShortener\Exceptions\NotFoundException;

class SqlitePDO
{

    /**
     * @var PDO
     */
    private $connection;
    /**
     * @var string
     */
    private $databaseFile;

    public function __construct(string $databaseFile)
    {
        $this->databaseFile = $databaseFile;
    }

    protected function getConnection() :PDO {
        if(!($this->connection && $this->connection instanceof PDO)){
            $this->connection = new PDO("sqlite:".$this->databaseFile);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }

        return $this->connection;
    }

    private function ensureResult($result) :bool {
        if(!$result)
            throw new NotFoundException("Record not found");
        return true;
    }
    private function getStatement(string $query, array $params =[]) :PDOStatement {
        $conn = $this->getConnection();
        $statement = $conn->prepare($query);

        foreach ($params as $key => &$val) {
            $statement->bindValue($key, $val);
        }
        return $statement;
    }

    public function fetchObject(string $query, callable $mapper, array $params =[]) {
        $statement = $this->getStatement($query, $params);
        $statement->execute();
        $statement->setFetchMode(PDO::FETCH_OBJ);
        $result = $statement->fetch();

        $this->ensureResult($result);
        return $mapper($result);
    }

    public function fetchList(string $query, callable $mapper, array $params =[]) {
        $statement = $this->getStatement($query, $params);
        $statement->execute();
        $statement->setFetchMode(PDO::FETCH_OBJ);
        $list = $statement->fetchAll();
        $objects = [];
        foreach ($list as $item)
            $objects[] = $mapper($item);

        return $objects;
    }

    public function exec(string $query) :int {
        $conn = $this->getConnection();
        return $conn->exec($query);
    }
}