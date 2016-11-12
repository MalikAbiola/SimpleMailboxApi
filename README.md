This an implementation of a simple email service API.
The content type for responses is application/json.

The result of a request to the API i.e. the response, will be directly reflected by the HTTP response code i.e. 2XX for successes, 4XX for user errors, 5XX for internal server errors.

Generally, successful responses (2XX’s) will be formatted like;
```
{
    'data' : [
        {
    
        },
        {
    
        },
    ], 
    'meta' : {
    }
}
```
The `data` key will contain the result of the request. In the case of a request that results in a collection, e.g. retrieving all mails, the data key will contain an `array` of individual items/objects. The meta key/field will only exist in this case to provide context to the result of the request. it contains pagination info, with fields such as total pages, page count, etc.

Other HTTP responses (4XX’s, 5XX’s) will have an `error` key/object instead of the `data` key/object which  will contain the `HTTP code`, the `error message`, and an `validation_error` field in the case of validation errors, which individually provide more context for the error.

Error responses are shown in the examples below;
```
{
    'error' : {
        'http_code' : 404,
        'message' : "Mail not found"
    }
}
```
#### OR
```
{
  "error": {
    "http_code": 400,
    "message": "Wrong Argument",
    "validation_errors": {
      "archive": {
        "message": [
          "The archive value must be provided"
        ]
      }
    }
  }
}
```
## Authorization
The API requires an authorization key for accepting requests. Authorization key should be provided in the request header with key `Authorization` and value as the authorization code (test code is 12345678). Not including an authorization code/token in the header with result in an HTTP Unauthorized response.
```
{
  "error": {
    "http_code": 401,
    "message": "Please provide an appropriate Authorization header."
  }
}
```

## Endpoints
This API provides four endpoints for interacting with the service.

### GET /mails
A `GET` request to this endpoint retrieves a paginated list of all mails. Each object of the message has a “read” field that indicates that the message has been read.
Additional parameters `page` and `limit` which accepts integer values can be used to paginate the result of the request.
##### Sample Request & Response
` curl -X GET -H "Authorization: 12345678" "http://api.oberlo.mailbox.dev/mails?page=1&limit=2" `
```
{
  "data": [
    {
      "uid": 21,
      "sender": "Ernest Hemingway",
      "subject": "animals",
      "message": "This is a tale about nihilism. The story is about a combative nuclear engineer who hates animals. It starts in a ghost town on a world of forbidden magic. The story begins with a legal dispute and ends with a holiday celebration.",
      "time_sent": "2016-03-29 08:24:27",
      "read": false,
      "created_at": "2016-11-12 20:20:51",
      "updated_at": "2016-11-12 20:20:51"
    },
    {
      "uid": 22,
      "sender": "Stephen King",
      "subject": "adoration",
      "message": "The story is about a fire fighter, a naive bowman, a greedy fisherman, and a clerk who is constantly opposed by a heroine. It takes place in a small city. The critical element of the story is an adoration.",
      "time_sent": "2016-03-29 10:52:27",
      "read": false,
      "created_at": "2016-11-12 20:20:51",
      "updated_at": "2016-11-12 20:20:51"
    }
  ],
  "meta": {
    "pagination": {
      "total": 6,
      "count": 2,
      "per_page": 2,
      "current_page": 1,
      "total_pages": 3,
      "links": {
        "next": "http://api.oberlo.mailbox.dev/mails?page=2"
      }
    }
  }
}
```
### GET /archives
This endpoint responds to a `GET` request and returns a paginated list of achieved mails with the “archived” field indicating the messages that have been archived. The read status of the mail is also returned via the read key. Passing the `page` & `limit` parameter in the query limits the result of the request.
##### Sample Request & Response
` curl -X GET -H "Authorization: 12345678" "http://api.oberlo.mailbox.dev/archives?page=1&limit=2" `
```
{
  "data": [
    {
      "uid": 22,
      "sender": "Stephen King",
      "subject": "adoration",
      "message": "The story is about a fire fighter, a naive bowman, a greedy fisherman, and a clerk who is constantly opposed by a heroine. It takes place in a small city. The critical element of the story is an adoration.",
      "time_sent": "2016-03-29 10:52:27",
      "read": false,
      "created_at": "2016-11-12 20:20:51",
      "updated_at": "2016-11-12 20:23:06"
    }
  ],
  "meta": {
    "pagination": {
      "total": 1,
      "count": 1,
      "per_page": 2,
      "current_page": 1,
      "total_pages": 1,
      "links": []
    }
  }
}
```
### GET /mails/id
This endpoint retrieves a mail specified by the ID.
##### Sample Request & Response
` curl -X GET -H "Authorization: 12345678" "http://api.oberlo.mailbox.dev/mails/21" `
```
{
  "data": {
    "uid": 21,
    "sender": "Ernest Hemingway",
    "subject": "animals",
    "message": "This is a tale about nihilism. The story is about a combative nuclear engineer who hates animals. It starts in a ghost town on a world of forbidden magic. The story begins with a legal dispute and ends with a holiday celebration.",
    "time_sent": "2016-03-29 08:24:27",
    "read": false,
    "archived": true,
    "created_at": "2016-11-12 20:20:51",
    "updated_at": "2016-11-12 20:23:51"
  }
}
```
### PATCH /mails/id
This endpoint is used to make updates to individual mails. Available options are `read` and `archive` which accepts only boolean values true or false.
##### Sample Request & Response
` curl -X PATCH -H "Authorization: 12345678" -H "Content-Type: application/json" -d '{"archive": true }' "http://api.oberlo.mailbox.dev/mails/21" `
```
{
  "data": {
    "status": true,
    "message": "Successful"
  }
}
```

### OTHER INFORMATION
This repo comes with a mail samples that can be easily imported to the database to try out the functionalities described in this documentation. Please follow the following steps to test the API.

1. Download the repo to your preferred web server
2. run `composer install` to install all dependencies
3. Create another copy of the `.env.example` file in the root directory as `.env`. Open the `.env` update your database configuration. Also provide a 32 character long `APP_KEY`.
4. From the CLI, go to the project root and run `php artisan import:test_messages` to import the sample mails.
5. Visit the endpoints using any means of your choice.
