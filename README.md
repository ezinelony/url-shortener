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
       From the application root, run php -S localhost:9000 -t src src/index.php

    Unit Tests Onyly
       From the application root, run ./vendor/bin/phpunit --colors ./test

    Integrated Tests Only
       From application root cd test/Integration, then run ./../../vendor/bin/behat 
    
    Unit and Integrated tests together
       From application root, run ./run-tests.sh 
        
    
## Not Doing
        - Not validating Urls in terms of reachability or that there are indeed Urls
        
## Example Usage
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
    
