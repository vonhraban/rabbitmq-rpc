### User data retrieval through rabbitMQ RPC call

#### Installation

To bring up the whole stack, install dependencies and start listening to connection:
```
 ./start.sh
```

This step involves the initial installation of dependencies. After this is done once,
the stack can be spin up with

```
docker-compose up -d
```
only

#### Usage

To start listening to messages in the db service

```
 make run_server
```

Keep in mind the exiting logic is not implemented in the server (service),
so to exit it a docker container needs to get killed. 
```
kill $(docker ps -q)
```

Nginx port forwards (hope this verb exists) 80 on itself to port 8080 on the 
local machine, therefore:

##### Getting a user by id
```
$ curl http://localhost:8080/getUser/42
{"first_name":"Harriette","last_name":"Mussolini","email":"hmussolini15@npr.org","gender":"Female","ip_address":"81.79.116.50"}
```

##### Querying a user that does not exist
```
$ curl -v http://localhost:8080/getUser/4815162342
*   Trying ::1...
* TCP_NODELAY set
* Connected to localhost (::1) port 8080 (#0)
> GET /getUser/4815162342 HTTP/1.1
> Host: localhost:8080
> User-Agent: curl/7.54.0
> Accept: */*
>
< HTTP/1.1 404 Not Found
< Server: nginx/1.15.5
< Date: Fri, 05 Oct 2018 14:40:59 GMT
< Content-Type: application/json
< Content-Length: 0
< Connection: keep-alive
< X-Powered-By: PHP/7.2.10
<
* Connection #0 to host localhost left intact
```

##### Unexpected response

I have implemented a logic in the db service to throw an exception if the
ID requested is negative (see UserStore::get). There is no fancy exception 
handling in there so the server would just spit a Generic Error in the response 
to the client. Client gets the message response that is unexpected, and gives a 500.
This is expected behaviour and logic is in place to extend functionality to handle
those cases gracefully when needed by utilising various response types.

```
$ curl -v http://localhost:8080/getUser/-1
*   Trying ::1...
* TCP_NODELAY set
* Connected to localhost (::1) port 8080 (#0)
> GET /getUser/-1 HTTP/1.1
> Host: localhost:8080
> User-Agent: curl/7.54.0
> Accept: */*
>
< HTTP/1.1 500 Internal Server Error
< Server: nginx/1.15.5
< Date: Fri, 05 Oct 2018 14:44:05 GMT
< Content-Type: application/json
< Content-Length: 0
< Connection: keep-alive
< X-Powered-By: PHP/7.2.10
<
* Connection #0 to host localhost left intact
``` 

##### Logging
The server outputs logging to stdout (as echo is used, although a proper logger should)

```
Received {"command":"getUser","payload":{"id":105}}
Replying with {"type":"UserData","payload":{"first_name":"Carola","last_name":"Raccio","email":"craccio2w@admin.ch","gender":"Female","ip_address":"17.197.75.166"}}
Received {"command":"getUser","payload":{"id":-1}}
Replying with {"type":"UnexpectedError"}
Received {"command":"getUser","payload":{"id":20000}}
Replying with {"type":"UserNotFound"}


```

#### Running tests

```
$ pushd .
$ cd api
$ ./vendor/bin/phpspec run
                                      100%                                       1
1 specs
1 example (1 passed)
24ms

$ cd ../db_service/
$ ./vendor/bin/phpspec run
                                      100%                                       1
1 specs
1 example (1 passed)
22ms

Vlads-MBP-2:db_service hraban$ popd
```

There is not a lot that can be unit tested in this project. Due to the way
RabbitMQ is used, and LeagueCSV is written, unit testing them in isolation
would require a massive amount of boilerplate mocking and make the implementation
really coupled to the tests, effectively making tests mirror the functionality
instead of testing it. Alternative option would be to create wrappers around
wrappers that wrap other wrappers around some another wrappers. But this would
quite probably make the maintainability suffer.

I would use proper integration tests here with preferably Behat/Gherkin. We
may not care in particular if a variable has changed its name or logic
is moved from one place to another, we want to know that a system, given A,
outputs Z. There are cases when unit testing is preferable, i.e. complicated
algorithm in an isolated place in code, but for this particular case this would be 
counterproductive. If invited for the interview, I would be happy to explain
the point in more detail.

#### Would be nice to have

API:

- Timeout handling in client
- Add integration tests to rabbit mq client
- Add lazy loading to the rabbit mq queue so that it does not load up when it is not needed
- Gender in User can be enum

Server:
- Use proper logger instead of echo. One would not do that in real life, but for the purposes of tech test it suffices
- Add integration tests to rabbit mq listener and CSV Reader


Both:
- I am sure code style is off in some places, so something like phpcs would be 
beneficial 