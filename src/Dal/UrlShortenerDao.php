<?php


namespace UrlShortener\Dal;


use UrlShortener\Models\UrlShortener;


interface UrlShortenerDao
{
    public function create(UrlShortener $model) :int;
    public function update(UrlShortener $model) :int;
    public function findById(string $id) :UrlShortener;
    public function findByShortenedUrl(string $shortenedUrl) :UrlShortener;
    public function delete(string $id) :int;
    /**
     * @return UrlShortener[]
     */
    public function findAll() :array;
}