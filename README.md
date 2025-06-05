### Follow these steps:

1. Install dependencies:

Run the following command to install all required PHP packages via Composer:

composer install

2. Configure the database connection:

Open the .env file and edit the database connection string to match your environment.

DATABASE_URL="mysql://username:password@localhost:3306/db_name"

3. Run migrations:

Run the following command to apply the database migrations and set up the schema:

php bin/console doctrine:migrations:migrate

4. Start the Symfony server:

Start the project with the built-in Symfony server:

symfony server:start

