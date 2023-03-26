# WebSocket Client

This is a websocket client that connects to a [websocket server](https://github.com/itzAymvn/websocket_server) and displays the messages it receives. It uses a SQL database to store the messages and a web server to serve the client.

## Requirements

-   PHP
-   MySQL

_You can use [XAMPP](https://www.apachefriends.org/index.html) to install PHP and MySQL on Windows._

## Installation and Setup

1. Clone the repository
2. Create a database and these import the tables from the `database.sql` file
3. Make sure the database credentials are correct in the `/db/dbConnect.php` file
4. Have the [websocket server](https://github.com/itzAymvn/websocket_server) running (Not necessary in the same machine)
5. Update the `/src/config.json` file with the correct websocket server URL

## Usage

After setting up the project, you can run the client by following these steps:

1. Run the server by running `php -S localhost:8080 -t .` in the root directory of the project
2. Open the client in your browser by going to `http://localhost:8080`

\* _You can change the port number in the `php -S` command_
