# url-shortener
Url Shortener Using Slim php framework

##Description
    This is a microservice to demo how Url shortener could work using
     PHP Slim framework and SQLite for storage

##Requirements
 * SQlite version >= 3.14.2
 * php version >= 7.0.0 
 * Composer 
 
## Running
    Dependencies 
       From the application root, run composer install  
    Migration
        From the application root, run ./config/migations/migrate.sh

    Server
       From the application root, php -S localhost:9000 -t src src/index.php

 ##Not Doing
        - Urls aren't validated in terms of reachability
        
 ##Example Usage
         Shorten and save a Url (Create/Replace)
           PUT /api/urls/{urlEncodedTargetUrl} (/api/urls/https%3A%2F%2Fwww.facebook.com%2F) 
            Optional body: 
            {"devices": {"mobile": "https://m.facebook.com/"}}
         
         Retrive saved Url 
            GET /api/urls/{urlEncodedTargetUrl} (/api/urls/https%3A%2F%2Fwww.facebook.com%2F)
            
         Delete saved Url 
            DELETE /api/urls/{urlEncodedTargetUrl} (/api/urls/https%3A%2F%2Fwww.facebook.com%2F)
            
         Retrive All saved Urls
            GET /api/urls
            
         Redirect to target by visiting shortened url
             GET|POST|PUT|DELETE|PATCH /{shortenedString} (/1a3rty)  
    
