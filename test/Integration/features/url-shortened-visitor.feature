Feature: Url Shortened Visitor
  In order to visit url targets
  As a client
  I need to be able to be redirected to url target when I visit the shortened url

  Scenario: Visit Shortened url from a desktop
    When I send a "GET" request to "aaaaaa?q=nelson" with userAgent "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2228.0 Safari/537.36"
    Then I should be redirected to "http://bbc.com"

  Scenario: Visit Shortened url from a mobile that has mobile device configured
    When I send a "GET" request to "eeeeee" with userAgent "Mozilla/5.0 (iPhone; CPU iPhone OS 5_0 like Mac OS X) AppleWebKit/534.46 (KHTML, like Gecko) Version/5.1 Mobile/9A334 Safari/7534.48.3"
    Then I should be redirected to "http://cnn.com"

  Scenario: Visit Shortened url that does not exist
    When I send a "GET" request to "zzzzzz" with userAgent "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2228.0 Safari/537.36"
    Then The response code should be "404"

  Scenario: Visit Shortened url that the target is  unreachable
    When I send a "GET" request to "bbbbbb" with userAgent "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2228.0 Safari/537.36"
    Then The response code should be "504"