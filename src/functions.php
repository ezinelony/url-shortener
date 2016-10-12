<?php

namespace UrlShortener;

function key(int $length = 6) {
    return substr(md5(uniqid(mt_rand(), true)), 0, $length);
}