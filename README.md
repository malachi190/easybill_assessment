Im Malachi Okpleya, this is my submission for the assessment.

To set up and run this application you will take the following steps.

First, clone the repository into a local directory

Install dependencies using the command "composer install" NB: you must have have Composer installed globally on your local machine

Set up environment variables by copying the content in .env.example, create a new .env file in the root directory and paste the copied content there.

Edit the .env file by configuring database information, set database to mysql and not sqlite which is the default.

Generate application key by running the command "php artisan key:generate"

Make sure database server is running and then migrate using the command "php artisan migrate".

Once that is done, start the server by running the command "php artisan serve"

Finally to check if all unit tests pass, run the command "php artisan test".

Lastly here is a link to the postman collection as instructed: https://www.postman.com/amal233/main-workspace/collection/2e9wkt3/easybill?action=share&creator=22788636
