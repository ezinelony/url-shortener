Feature: Url Shortener
  In order to shorten urls
  As a client
  I need to be able to manage shortened url entities

  Scenario: Create and Save Shortened Url that does not exist
    Given I have the JSON payload
    """
    {
        "devices": {
        "mobile": "http://m.facebook.com",
        "tablet": "http://facebook.com"
        }
    }
    """
    When I send a "PUT" request to "api/urls/http%3A%2F%2Ffacebook.com"
    Then The response code should be "201"
    And The response content type should be "application/json;charset=utf-8"

  Scenario:  Save Shortened Url that already exists
    Given I have the JSON payload
    """
    {
        "devices": {"mobile": "http://m.facebook.com"}
    }
    """
    When I send a "PUT" request to "api/urls/http%3A%2F%2Fwww.iitd.ac.in"
    Then The response code should be "204"
    When I send a "GET" request to "api/urls/http%3A%2F%2Fwww.iitd.ac.in"
    Then The response code should be "200"
    And The response content type should be "application/json;charset=utf-8"
    And The field "devices" in response should equal json
    """
    {
      "mobile": "http://m.facebook.com"
    }
    """

  Scenario:  Delete Shortened Url that already exists
    When I send a "DELETE" request to "api/urls/http%3A%2F%2Fwww.iitd.ac.in"
    Then The response code should be "204"
    When I send a "GET" request to "api/urls/http%3A%2F%2Fwww.iitd.ac.in"
    Then The response code should be "404"
    And The response content type should be "application/json;charset=utf-8"


  Scenario:  Delete Shortened Url that does not already exists
    When I send a "DELETE" request to "api/urls/http%3A%2F%2Fwww.youtube.com"
    Then The response code should be "404"
    And The response content type should be "application/json;charset=utf-8"


  Scenario:  Find Shortened Url that already exists by the url target
    When I send a "GET" request to "api/urls/http%3A%2F%2Findependent.com"
    Then The response code should be "200"
    And The response content type should be "application/json;charset=utf-8"
    And The response should equal json
    """
    {
      "createdAt": "2016-10-13T14:25:32+00:00",
      "updatedAt": "2016-10-13T14:25:32+00:00",
      "originalUrl": "http:\/\/independent.com",
      "url": "http:\/\/localhost:9000\/eeeeee",
      "devices": {
        "mobile": "http:\/\/cnn.com"
      }
    }
    """


  Scenario:  Find Shortened Url that does not already exists by the url target
    When I send a "GET" request to "api/urls/http%3A%2F%2Fwww.youtube.com"
    Then The response code should be "404"
    And The response content type should be "application/json;charset=utf-8"

  Scenario:  Fetch all shortened urls
    When I send a "GET" request to "api/urls"
    Then The response code should be "200"
    And The response content type should be "application/json;charset=utf-8"
    And The response should equal json
    """
      [
        {
        "createdAt": "2016-10-13T14:25:32+00:00",
        "updatedAt": "2016-10-13T14:25:32+00:00",
        "originalUrl": "http:\/\/bbc.com",
        "url": "http:\/\/localhost:9000\/aaaaaa",
        "devices": {}
        },
        {
            "createdAt": "2016-10-13T14:25:32+00:00",
            "updatedAt": "2016-10-13T14:25:32+00:00",
            "originalUrl": "http:\/\/not-reachable.organ",
            "url": "http:\/\/localhost:9000\/bbbbbb",
            "devices": {}
        },
         {
            "createdAt": "2016-10-13T14:25:32+00:00",
            "updatedAt": "2016-10-13T14:25:32+00:00",
            "originalUrl": "http:\/\/github.com",
            "url": "http:\/\/localhost:9000\/cccccc",
            "devices": {}
        },
        {
          "createdAt": "2016-10-13T14:25:32+00:00",
          "updatedAt": "2016-10-13T14:25:32+00:00",
          "originalUrl": "http://www.iitd.ac.in",
          "url": "http://localhost:9000/dddddd",
          "devices": {}
        },
         {
            "createdAt": "2016-10-13T14:25:32+00:00",
            "updatedAt": "2016-10-13T14:25:32+00:00",
            "originalUrl": "http:\/\/independent.com",
            "url": "http:\/\/localhost:9000\/eeeeee",
            "devices": {
                "mobile": "http:\/\/cnn.com"
            }
        }
    ]
    """