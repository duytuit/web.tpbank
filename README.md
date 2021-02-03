Installing

```
Install the application dependencies

```
composer install
npm install
```

Create the environment configuration file and edit it with your favorite IDE

```
cp .env.example .env
```

Set your application key

```
php artisan key:generate
```

Generate your JSON Web Token key

```
php artisan jwt:secret
```

Run database migrations

```
php artisan migrate
```

Execute the NPM script

```
npm run dev
```

Change the group ownership of the storage and cache directories and grant them all permissions (for Mac type `_www` instead of `www-data`)

```
sudo chgrp -R www-data storage bootstrap/cache
sudo chmod -R 777 storage public/uploads public/images bootstrap/cache
```

Install the application (create default roles, permissions, etc.)

```
php artisan install

