# Project Setup Guide

## Prerequisites
- Docker & Docker Compose
- Node.js & npm
- Composer

## Installation Steps

1. **Clone the repository**
   ```sh
   git clone git@github.com:guimossibento/temp-app.git
   cd temp-app
   ```

2. **Set up environment variables**
   Copy the example environment file and configure it accordingly:
   ```sh
   cp .env.example .env
   ```

3. **Start the Docker containers**
   ```sh
   docker-compose up -d --build
   ```
   Wait for the commands to run and you'll be able to access the application


5. **Access the application**
    - Application: [http://localhost:8080](http://localhost:8080)
    - Database runs on port `3307`

## Features

- **Laravel Backend**: Handles API requests and business logic.
- **MySQL Database**: Stores application data.
- **Nginx Web Server**: Serves the application.
- **Automated Setup**: Uses a script (`entrypoint.sh`) to install dependencies and run migrations.
- **Supervisor for Queue Management**: Ensures background jobs run continuously.
- **City Management**: Users can create and manage cities using the cities route and form.
- **Automated Temperature Logging**: The system automatically records temperature data for each registered city every hour.
- **Temperature Dashboard**: Users can view recorded temperatures via the dashboard endpoint or home page.

## Usage

- **Create a new city:**
    - Use the web form at cities endpoint and create a new city to track:

- **View Temperature Data:**
    - Temperature records can be accessed through the dashboard endpoint or home page.
    - The system automatically records a temperature entry for each registered city every hour.


## Future improvements

- Implement authentication to restrict city additions to authorized users.
- Develop a real-time system to update graphics when the schedule interval is reduced.
- Use a different API to fetch city data, as the current one is missing some cities.
- Create a display to show the current temperature alongside historical data.
- Allow users to update the temperature unit (currently, only Celsius is supported).
