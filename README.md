#Steps to use localhost

1: First download or clone it using

   git clone https://github.com/victorcarlojacaban/twitter-app.git

2: Type the following command: composer install

3: stall npm dependencies: npm install

4: Start the watch: npm run watch

5: Create a database and configure inside the .env.example file.

6: rename .env.example to .env and save it

7: Migrate the database: php artisan migrate

8: Now you can check it by running 

   php artisan serve

9: Access home vie http://localhost:8000/home
10: Access user profile http://localhost:8000/users/{user firstname}
11. Profile page http://localhost:8000/profile