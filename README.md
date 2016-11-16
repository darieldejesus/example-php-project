# RESERVATION IO - RESTFUL API

Reservation IO API is a little project I have created as a technical evaluation. It consists on a few endpoints which allow you create, read, update and delete ***Users*** and their ***Reservations***. If you want to test this project, follow the steps under *Installation*.


## Installation
Requirements:
  - VirtualBox 64bits
  - Vagrant
  - Github (Optional)


Steps:

1.  Install *VirtualBox* 64bits and *Extension Pack* (Recommended).

    Reference: https://www.virtualbox.org/wiki/Downloads

2.  Install Vagrant.
    
    Reference: https://www.vagrantup.com/downloads.html

3.  Download project to your local environment.

    Download ZIP:
    ```sh
    $ wget https://github.com/darieldejesus/example-php-project/archive/develop.zip
    ```
    Or you can clone the project locally:
    ```sh
    $ git clone git@github.com:darieldejesus/example-php-project.git
    ```

4.  Go to *vagrant* directory and start the virtual machine.
    ```sh
    $ cd vagrant/
    $ vagrant up
    ```
    After this step, ***Vagrant*** would setup the virtual dev environment automatically.

5.  After virtual dev environment is setup, login into the machine to confirm it is working.
    ```sh
    $ cd example-php-project/vagrant/
    $ vagrant ssh
    $ cd /home/www
    ```
    Here you would see all files related to the project.

6. In order to access to the API via a local domain, you need to add this entry in the ***hosts*** file:

    OSX/Linux
    ```sh
    $ sudo su
    $ echo '192.168.1.123   reservation.io' >> /etc/hosts
    ```

7. The latest step is run Unit tests to confirm everything is working as expected:

    ```sh
    $ cd example-php-project/vagrant/
    $ vagrant ssh
    $ cd /home/www
    $ phpunit
    ```

## How to use the API

Basically the API contains two main entities (***User*** and ***Reservation***) and you can manage them using these endpoints:

|URI|Method|Action|
|---|---|---|
|/api/v1/users|**POST**|Create a new User|
|/api/v1/users/[id]|**GET**|Return an User by [id]|
|/api/v1/users/[id]|**PUT**|Update an User by [id]|
|/api/v1/users/[id]|**DELETE**|Remove an User by [id]|
|/api/v1/users/[id]/reservations|**GET**|Return all reservations by User [id]|
|/api/v1/reservations|**POST**|Create a new Reservation|
|/api/v1/reservations/[id]|**DELETE**|Remove a Reservation by [id]|
|/api/v1/users/recommendations/[id]|**GET**|Return all users around User by [id]|

### POST /api/v1/users
Create an User: POST http://reservation.io/api/v1/users

Request body:
```json
{
 "email" : "test@company.com",  
 "name" : "John Doe",  
 "first_name" : "John",  
 "last_name" : "Doe Curtis",
 "age" : 15,
 "host" : false,
 "birth_date": "1979-06-09" 
}
```

Response body:
```json
{
  "code": 200,
  "status": "OK",
  "message": "success",
  "response": {
    "email": "test@company.com",
    "name": "John Doe",
    "first_name": "John",
    "last_name": "Doe Curtis",
    "age": 15,
    "host": false,
    "birth_date": "1979-06-09",
    "updated_at": "2016-11-16 22:59:36",
    "created_at": "2016-11-16 22:59:36",
    "id": 31121
  }
}
```

### GET /api/v1/users/[id]
Return an User by id: GET 

Response body: http://reservation.io/api/v1/users/31121
```json
{
  "code": 200,
  "status": "OK",
  "message": "success",
  "response": {
    "id": 31121,
    "first_name": "John",
    "last_name": "Doe Curtis",
    "age": 15,
    "birth_date": "1979-06-09",
    "host": 0,
    "name": "John Doe",
    "email": "test@company.com",
    "created_at": "2016-11-16 22:59:36",
    "updated_at": "2016-11-16 22:59:36",
    "latitude": "",
    "longitude": ""
  }
}
```

### PUT /api/v1/users/[id]
Update an user property: PUT http://reservation.io/api/v1/users/31121

Request body:
```json
{
 "name" : "John Doe",  
 "first_name" : "John"
}
```

Response body:
```json
{
  "code": 200,
  "status": "OK",
  "message": "success",
  "response": {
    "id": 31121,
    "first_name": "John",
    "last_name": "Doe Curtis",
    "age": 15,
    "birth_date": "1979-06-09",
    "host": 0,
    "name": "John Doe",
    "email": "test@company.com",
    "created_at": "2016-11-16 22:59:36",
    "updated_at": "2016-11-16 22:59:36",
    "latitude": "",
    "longitude": ""
  }
}
```

### DELETE /api/v1/users/[id]
Remove an User: DELETE http://reservation.io/api/v1/users/31121

Response body:
```json
{
  "code": 200,
  "status": "OK",
  "message": "success",
  "response": true
}
```

### POST /api/v1/reservations
Create a new Reservation: POST http://reservation.io/api/v1/reservations

Request body:
```json
{
 "host_id": 31122,
 "guest_ids": [22117, 22118, 22119]
}
```

Response body:
```json
{
  "code": 200,
  "status": "OK",
  "message": "success",
  "response": true
}
```

### GET /api/v1/users/[id]/reservations
Get the reservations from an User: GET http://reservation.io/api/v1/users/31122/reservations

Response body:
```json
{
  "code": 200,
  "status": "OK",
  "message": "success",
  "response": {
    "reservations": [
      {
        "reservation_id": 15,
        "host": 31122,
        "guest": 22117
      },
      {
        "reservation_id": 16,
        "host": 31122,
        "guest": 22118
      },
      {
        "reservation_id": 17,
        "host": 31122,
        "guest": 22119
      }
    ]
  }
}
```

### DELETE /api/v1/reservations/[id]
Remove a Reservation: DELETE http://reservation.io/api/v1/reservations/15

Response body:
```json
{
  "code": 200,
  "status": "OK",
  "message": "success",
  "response": true
}
```

### GET /api/v1/users/recomendations/[id]
Return all Users arround current User by id: GET http://reservation.io/api/v1/users/recommendations/22129

Response body:
```json
{
  "code": 200,
  "status": "OK",
  "message": "success",
  "response": {
    "user_ids": [
      22121,
      22125,
      ...
    ]
  }
}
```
