# Maps Ton Sandwich
An innovative website to create and share the sandwiches with the toppings you like!

> created by Samra Abdul, Pauline Auda, Amandine Brémont and Mathilde Turra


Features : 
- Registration and authentication system
- Mailer and reset password system
- Mapping system — ie. creating your own sandwich
- Cart system
- Payment system
- Publications feed 
- Bookmark system
- Reservation system
- Admin back-end interface

## Class Diagram

## Languages

## Settings

1. Clone or fork the MAPS repository on your local machine.
2. In the .env file, comment line 32 and uncomment line 31 : fill the app with your database name user, !ChangeMe! with your database password and app with your database name.
3. Install Mailhog : https://github.com/mailhog/MailHog/releases, run the .exe file and go to http://localhost:8025/ to access your inbox.
4. In your project directory terminal, run the following commands : 
```console
composer require symfony/apache-pack
```
```console
composer require symfony/runtime
```
```console
composer require symfony/webpack-encore-bundle
```
```console
symfony console doctrine:database:create
```
```console
symfony console doctrine:migrations:migrate
```
```console
symfony console doctrine:fixtures:load
```
```console
npm install
```
```console
npm run build
```

![protocole](mapsdigramme.PNG)

You're now free to enjoy our MAPS website!
