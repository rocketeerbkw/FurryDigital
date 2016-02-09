# FurryDigital

* [Overview](#overview)
* [Developing Locally](#developing-locally)
* [Code Reference Guide](#code-reference-guide)

## Overview

**FurryDigital** is the flagship web application for the FurryDigital web site. It is built on the solid foundation of the [FAOpen](https://github.com/SlvrEagle23/FAOpen) project, with a number of improvements and unique branding.

The application is built on the following technologies:

* [Composer](https://getcomposer.org/) (Dependency Management)
* [Phalcon](http://phalconphp.com/en/) (PHP MVC Framework)
* [Doctrine 2](http://www.doctrine-project.org/) (PHP Database Layer)
* [Vagrant](http://www.vagrantup.com/) (Local Development)
* [Sass](http://sass-lang.com/) (Stylesheets)

## Why FurryDigital?

With the fandom's leading web site continuing to exist and serve users on its current codebase, many might ask: why bother helping to support FurryDigital?

Although the FAOpen codebase was originally intended for FurAffinity's use, their internal structure makes it highly unlikely that the code will ever be used there. The FurryDigital project is an effort to release that hard work to the public, allow the public to see and participate in the project with full transparency, and deliver a high quality service to the fanbase.

The advantages of the FurryDigital platform are growing every day, but already, this new application is:

* **More modern:** FurryDigital is built on top of modern, object-oriented PHP using the very powerful and fast Phalcon framework. Today's coding best practices, from MVC to Dependency Injection, are par for the course in this code. New tools like Vagrant, Composer, Sass and even Git itself help make the development process easier.
* **More secure:** Data safety and privacy is a top priority in this application. User passwords are hashed using PHP7's newest bcrypt-powered salted and hashed passwords, private messages are stored encrypted in the database, data validation (both client and server-side) are built into every form, including Cross Site Request Forgery (CSRF) protection. 
* **More transparent:** All of FurryDigital's code is right here in this repository for you to see and inspect for yourself. If you spot a vulnerability, you can submit a pull request yourself to help fix the problems. We place this kind of trust in our fans because we are driven by serving the public interest, not a bottom line or profit motive. In the end, this means better code with more frequent updates for the users.
* **More powerful every day:** Already, the application features a number of new changes (bigger thumbnails, fewer advertisements, more permissive uploading rules) and connections to third-party services (Gravatar, sign-on with Google/Facebook/etc) than the FAOpen codebase. With a community helping to contribute new features and address issues in the code, more new features can arrive sooner, with smarter testing and review from more experts. 

We hope you're as excited to work alongside us to help make this a great home for the art and multimedia of the fandom as we are.

## Developing Locally

Follow these steps to get started with the local development process:

* Clone this repository to your hard drive.
* Install [Vagrant](http://www.vagrantup.com/) for your OS.
* Install [VirtualBox](https://www.virtualbox.org/wiki/Downloads) for your OS.
* Open a command line prompt at the root of this repo.
* Type `vagrant up` in the command line.

If you don't already have the Vagrant box downloaded, this process may take several minutes (or even hours, depending on your bandwidth). The box image is cached locally, though, making future vagrant runs easy.

**Note**: Some credential files are not included in this repository. Default files will automatically be generated in order to allow the web site to function properly. In order to use third-party services, contact an existing developer for the credentials files.

### SSH

You can connect to the Vagrant VM by typing `vagrant ssh` into the command line of the host computer.

### Web Server

The web server is configured by default to respond to `http://localhost:8080/`.

The web application resides by default in the `/var/www/vagrant/` directory inside the Vagrant virtual machine.

### Database

MySQL can be accessed directly by connecting to the VirtualBox instance via SSH tunnel, using the SSH username `vagrant` and password `vagrant`.

The default MySQL `root` password is `password`.

### Administrator Account

When Vagrant is spun up initially, a super-administrator account is created for easy access to the site.
 
The default administrator username is `admin` and password is `admin`.

### Common Tasks

The Vagrant virtual machine is automatically configured with Composer, Node.js and other important tools pre-installed.

Because stylesheets are written in SCSS, they must first be compiled into CSS before changes will be visible in the browser. We strongly recommend a tool like [Koala](http://koala-app.com/) (Free) or [Compass.app](http://compass.kkbox.com/) (Paid) to handle this task. Both can be pointed at the `web/static/sass` folder, and should automatically build files inside `web/static/css`. **Do not edit the files in `web/static/css` directly!**

## Code Reference Guide

### MVC Structure

The modern application follows the Model/View/Controller (MVC) structure, which enforces a strict separation between the database model (M), the controlling code that connects the model and template (C), and the template itself (V).

Typically, the URL pattern is as follows: `furry.digital/{module}/{controller}/{action}/{parameters}`, which corresponds to the following path: `/app/modules/{module}/controllers/{controller}Controller.php::{action}Action()`.

The default module is `frontend`, default controller is `index` (`IndexController.php`) and the default action is `index` (`indexAction`).

Views are managed using the [Volt template engine](https://docs.phalconphp.com/en/latest/reference/volt.html). Volt, a template specific to the Phalcon framework, is based very closely on the Jinja template engine. Code editors like PhpStorm can process the syntax if the template is set as a "Twig" template (Symfony's template engine).

### The Dependency Injector

Common functionality is managed in a common container called the [Dependency Injection](https://docs.phalconphp.com/en/latest/api/Phalcon_DI.html) (DI) container. You can access all common functionality from any controller, view or elsewhere in the code using the following code block:

```php
<?php
$di = \Phalcon\Di::getDefault();
$auth = $di->get('auth'); // OR...
$auth = $di['auth'];
?>
```

**Inside a controller**, all DI containers are automatically injected into controllers. They can be called using `$this->{container_name}`, i.e. `$this->auth->isLoggedIn()`.

**Inside a view**, DI containers are automatically resolved as variables. They can be called using `{{ container_name }}`, i.e. `{{ auth.isLoggedIn() }}`.

Here are the common DI containers used across the system:

* **`auth` - Authentication:** Tracks whether a user is logged in, and can return the current active user.
* **`acl` - Access Control List:** Checks whether a user has permission to access a specified feature.
* **`cache` - Cache:** Access user cache data (stored in flat files, Memcached, or Redis).
* **`config` - Configuration:** Access global configuration files as objects.
* **`csrf` - CSRF Protection:** Dedicated class to generate tokens for Cross-Site Request Forgery protection.
* **`current_module_config` - Current Module Configuration:** Same as `config`, but relative to the current module.
* **`db` - Doctrine Database Abstraction:** The [Database Abstraction Layer](http://www.doctrine-project.org/projects/dbal.html) from the Doctrine database system.
* **`em` - Doctrine Entity Manager:** The global ORM [Entity Manager](http://www.doctrine-project.org/api/orm/2.0/class-Doctrine.ORM.EntityManager.html) from the Doctrine database system.
* **`fa` - FurryDigital Legacy:** Inherited functionality from the older FurryDigital system.
* **`flash` - Status Announcements:** Post persistent messages to the site header, that will appear either on the current page load (if the render hasn't happened yet) or on the next page (in the event of forwards).
* **`parser` - Text Parser:** Helpers to handle message escaping, BBCode, smileys, URLs, etc.
* **`session` - Session Management:** Wraps the PHP $_SESSION superglobal and allows for isolated namespaces in the session. 
* **`url` - URL Routing:** Tools for generating dynamic and static URLs (particularly `$url->route()` and `$url->get()`).
* **`user` - Current User:** The currently logged in user model, or an empty placeholder for anonymous users.
* **`view` - Template System:** The current view and all associated variables.
* **`viewHelper` - View Helpers:** Helper classes located in `app/library/FA/View/Helper`.