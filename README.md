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
    $ cd vagrant/
    $ vagrant ssh
    $ cd /home/www
    ```
    Here you would see all files related to the project.
