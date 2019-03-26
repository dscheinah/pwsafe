# sx|pwSafe

This is a password safe to be used a self hosted web application. 
It is implemented using modern PHP, MySQL and JavaScript. Of course some HTML and CSS is also needed.

The current state of development currently is alpha. 
This means the basic parts of the application are working, but are not tested very well.
Also some features are still under development:
* user and group management with basic privileges
* share passwords with other users and groups
* add more options to the generator
* write a bookmarklet for cross browser integration on websites

This readme gives information about 
[Requirements](#requirements), 
[Deployment](#deployment) and 
[Development](#development). 

It is does not contain user documentation for end users. I also do not intend to create one.
Also for now the application is only available in german language and has no translations ready. Sorry.

## Requirements

The application should be used with a modern and up to date browser. 
It is heavily tested with Firefox, but others as Chrome, Edge or Safari should also work.

For server side requirements see the next section about deployment.

## Deployment

This is a summary on how to deploy the application to your server.
Since development is not finished yet, there is no released version yet.
All information inside this section are based on the current development deployment. 
But in theory it is also usable for production.

If you use the files provided in `sql/` to initially fill your database for both deployment variants.
To perform updates on an existing database, execute only the files not yet executed in your environment. 
A user will be added named `default` who can login with any password. The password should of course be set afterwards.

### Docker

The easiest way to deploy the application is using Docker. 
A corresponding `Dockerfile` is available. 
It must be used in combination with a MySQL companion. Also adding SSL support is highly encouraged.
The provided `docker-compose.yml` is created for development. 
But all environment configuration and documentation can also be used for production deployment.

### No Docker

If have no Docker available you need to setup a stack containing of a web server with PHP and a MySQL server. 

The document root of your web server must point to `src/public`.
To use the provided .htaccess use a recent Apache as your web server. 
Otherwise you need to convert the provided configuration.

As for PHP at least version 7.2 is required.

Additionally to the setup of the server applications you need to prepare the application. 
This can be done as described in the development section following this one.

Also you may want to minify the CSS and JavaScript and insert the templates inside the index.html file.
These must be wrapped inside a template with the ID matching the file name without extension.
See the `Dockerfile` and `build.js` for more information.

## Development

If you want to implement features for yourself, here is a quick list of information to get you started.

### Preparation

To get a working application you need to first initialize your application.
This is done in two steps:
1. Create the configuration in `src/config` by copying the default `src/config/config.local.php.dist`
2. Use `composer install` in the `src` directory to load the PHP dependencies

If you have Docker available you are done. User use the provided `docker-compose.yml` to start your development servers.
Remember to create a `.env` for it as described in the file.

If not you need to setup your server stack according to the previous deployment section.

### Overview 

* Domain Code for the PHP backend (JSON only) is found in `src/app`
* HTML is mostly isolated to the templates in `src/public/templates`
* Client interaction is pure JavaScript starting with `src/public/js/app.js`
* Startup of the browser side application is defined in `src/public/index.html`
* Requests to PHP routes are rewritten to `src/public/index.php` 
* To get an overview over the database structure see `sql/setup.sql`
* All CSS style is defined in files located in `src/public/css`
