# Paul's Cookbook

This is a simple recipe app. It is made with Symfony 6 for the backend API, with JWT authentication. For the Databse it is using a MySQL 8 database.

The frontend is a VUE 3 app.

All these are running in a docker compose stack.

# User Credentials

There are 3 users defined in the app:

**Admin**
```json
{
    "email": "admin@example.com",
    "password": "admin"
}
```

**Aoife Kelly**
```json
{
    "email": "aoife.kelly@example.com",
    "password": "test"
}
```

**Liam O'Sullivan**
```json
{
    "email": "liam.osullivan@example.com",
    "password": "test"
}
```
# App Features

There is a list of Recipes that any user can see they don't need to be logged in.

Any public user can view a recipe, it's details, average rating and the comments.

To leave a comment and a rating a user needs to be logged in. There is a button in the header of the site to log in.

The admin user is the only user that can create a new recipe or can edit an existing recipe.

# Setup Instructions

This project requires `docker` and `docker compose` to be setup 

After cloning the repo run the command below to start the docker containers

```
docker compose up --build
```

Then navigate to the `backend` container with

```
docker compose exec backend bash
```

In there run the command below to install the symfony packages

```
composer install
```

Then update the database with

```
php bin/console doctrine:migrations:migrate --no-interaction
```

Then load the demo data into the database
```
php bin/console doctrine:fixtures:load --no-interaction
```

To access the containers use the following urls:
* http://localhost:8000/health - to check the symfony API status
* http://localhost:8080 - for the phpmyadmin to view the database
* http://localhost:5173 - for the Vue app