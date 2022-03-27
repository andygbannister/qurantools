# Qur’an Tools

Qur’an Tools is a digital tool for the critical analysis and study of the text of the Qur’an.

## Team

Qur’an Tools has been designed and programmed by Dr. Andrew G. Bannister and Dr. R. Michael McCoy with help from other developers.

## Copyright

Qur’an Tools is an open source project and is available under the GNU public license with terms of use.

## Transliteration

The Qur’an transliteration used is based in part upon the Tanzil Qur’an Text (Uthmani, version 1.0.2); Copyright © 2008-2009 Tanzil.info; License: Creative Commons BY-ND 3.0.

## Morphological Tagging

Qur’an Tools makes some use data from the Quranic Arabic Corpus developed at the University of Leeds by Kais Dukes. It is released under the GNU License. You can download a version of it from <https://corpus.quran.com/download/>.

## Dictionary

Dictionary data derived in part from Project Root List (<http://www.studyquran.co.uk/PRLonline.htm>), which has digitised several classical Arabic dictionaries (Al-Mufradaat fi Ghariib al-Qur'aan, Lisaan al-Arab, Taaj al-Aruus min Jawaahir al-Qaamuus, and An Arabic-English Lexicon by E.W Lane) and made the data publicly available.

## Technical Information

### How to install

Qur'an Tools is a pretty simple bare-metal PHP application. These are the general steps required for installation. See later sections for more detail.

1. Install a webserver (probably Apache2), PHP7.3. MySQL 8 (or Maria DB 10.3), composer 2, Git and (optionally) Node with npm onto your server.
1. Git clone this repo into a folder on your server such as `/home/qurantools/qurantools.acme.org`.
1. Configure Apache to be able to access the `app` folder of this repo with the web-facing URL you want for the application, say `https://qurantools.acme.org`
1. Create a database and user in MySQL or MariaDB, and populate it from the import found in `/database/install`.
1. Manually create an admin user in the `USERS` table of the database.
1. Install composer and node dependencies
1. Configure PHP to send emails from your server.
1. Get Google tag manager tag and ReCaptcha keys from Google
1. (optional) Put a privacy and cookies policy document on the internet somewhere. It could be somewhere this app's folder structure, or may be a link to other policies for your institution.
1. (optional) Create .htaccess file for 404 error redirects
1. Fill out all the fields in `qt.ini` with details from the above steps. You will want to keep `is_user_registration_allowed` as false until you are sure all is working OK.
1. Browse the site. When asked to login, press "forgot password" and use the link that will be emailed (or shown on screen) to reset it.

### Base software install

#### Webserver config

Although nginx should work fine for this application, but all development and deployment has been done with Apache2.

#### PHP config

The app should run on any version of PHP 7 or higher - but it has been most thoroughly tested on 7.3.

Some of the composer development tools require extra PHP modules to be installed, but if applicable, most of these will be explained when you try to run `codecept` for development testing. Extra modules that codeception may not report on include `xdebug`, `mysql` and `intl`.

If you want to get xdebug working for local development, you'll need to ensure something like `zend_extension=xdebug` is in your `php.ini` and CLI `php.ini` files. See <https://xdebug.org/docs/install>.

Some of the search queries in Qur'an Tools use a lot of memory. You may need to set `memory_limit` in `php.ini` to `128M` to prevent large queries from failing.

#### Database config

At time of writing, Qur'an Tools uses Maria DB 10.3. Many of the queries in the initial application were written in a more traditional MySQL mode that is less strict about GROUP BY clauses as per <https://dev.mysql.com/doc/refman/8.0/en/group-by-handling.html>. In order for the application to run with later versions of MySQL and MariaDB that are ANSI or SQL-92 compliant, `my.cnf` will need this line added to it:

```ini
[mysqld]
sql_mode=TRADITIONAL
```

See <https://dev.mysql.com/doc/refman/8.0/en/sql-mode.html#sql-mode-combo> for more info.

### Git Clone

If you need help forking and cloning this git repo, consult the GitHub documentation. <https://docs.github.com/en/get-started/quickstart/fork-a-repo>.

We would recommend forking this repo and cloning your fork onto your server as there are likely to be a few customisations you will need to make, especially for branding.

```bash
> cd /folder/on/your/server                              # e.g /home/qurantools/www/
> git clone git@github.com:yourname/your-qt-fork.git     # creates a folder called your-qt-fork
```

It is quite a large repo because it also contains rather a lot of binary images and PDF resources in addition to the source code and database data.

Extra binary resources are found here:

```bash
app/dictionary/**/*.pdf  # PDFs of dictionary pages
app/texts/*.pdf          # PDFs of more obscure inter-linear texts
```

### Configure Apache

This totally depends on your own server set-up. But you'll likely want to make sure you know where the `error_log` is for trouble-shooting later in the process. The `app` folder is the point of entry to the application.

```conf
...
# /etc/apache2/qurantools_acme_org.conf

DocumentRoot /home/qurantools/www/your-qt-fork/app/

    <Directory /home/qurantools/www/>your-qt-fork/app/>
        Require all granted
        # Allow local .htaccess to override Apache configuration settings if needed
        AllowOverride all
    </Directory>
...
```

### Create and populate database

Due to Arabic character encodings, you'll need a database with utf16 encodings.

Use whatever database name you wish; `qurantools` is just an example.

```SQL
mysql> CREATE DATABASE IF NOT EXISTS `qurantools` DEFAULT CHARACTER SET utf16 COLLATE utf16_bin;
```

You'll also need to create a database user with access to this database, say `qurantools`.

The database of Qur'anic information then needs to be imported into this database.

```bash
> cd qt_root/database/install
> mysql -u <database-user> -p <database-name> < install_tables.sql
```

If `QURAN-FULL-PARSE` has trouble recreating the primary key index, try splitting up the index create command. Depending on your version of MySQL, you may see a few warnings during the import phase. They are unlikely to be important - but it may pay to check the import log just to be on the safe side.

After that, you will need to run any migrations (if there are any) in `/database/migrations/` to apply any database changes that were made to the database since the original schema was created.

### Install 3rd party Dependencies

#### Composer

There are is only one 3rd party PHP library that QT uses for production use (Google ReCaptcha), plus other libraries that are only used in development (e.g. Codeception). Currently, ReCaptcha is a requirement, but you can edit this out if you like (see `/app/auth/request_password_reset`). But to minimise spammers and password hackers hitting your server, a ReCaptcha solution is recommended. Libraries are specified in `composer.json`. To install/update these libraries run as the owner of the `vendor` directory (but not `root`):

```bash
> cd /project/root  # e.g cd /home/qurantools/www/your-qt-fork/ - should contain composer.json
> php composer.phar install --no-dev  # no development dependencies
> php composer.phar install           # all dependencies
> composer install --no-dev           # same as above, but only works if composer is installed glob
```

You may need to temporarily set `allow_url_fopen` in the relevant `php.ini` to `Off` before running this command. `composer.phar` will need to be in the root folder, and can be installed from <https://getcomposer.org/download/>.

`composer install` may fail if you are running a version of PHP that is different what was running when `composer.lock` was built. Composer will tell you what the problem package is, and you should be able to get round it by updating it. e.g `composer update psr/container`

#### Node

The node packages are only required for development.

```bash
> cd /project/root  # e.g cd /home/qurantools/www/your-qt-fork/ - should contain package.json
> npm install
```

### Create first admin user

To add the first admin user to the database, run some SQL like this:

```SQL
INSERT INTO `qurantools`.`USERS` (
        `Email Address`,
        `First Name`,
        `Administrator`
    )
VALUES (
        'qurantools@acme.org',
        'Admin',
        'SUPERUSER'
    );
```

You'll be able to set the password for this account later once you've configured email sending. The reset password link is at `/auth/request_password_reset.php`.

### Configure PHP email sending

Quran Tools needs to send the occasional email out to users. The most important of these are for user password resets, but it can be configured to send error/debugging messages to the admin/developers using the `email_qt()` and `email_error_to_qt()` functions. Currently, Quran Tools uses PHP's `mail()` tool - which is configured in `php.ini` with `sendmail_path` et al. We are aware the there are better mailing solutions (like [PHPMailer](https://github.com/PHPMailer/PHPMailer)).

Run `php -i` and look at the top of the output to see what `php.ini` file you need to edit.

### Google Keys

- (Optional) Quran Tools has an option for capturing Google analytics usage (and anything else Google related) via [Google Tag manager](https://tagmanager.google.com/). If you want to use this, then get a Google Tag Manager Code and use it for config in `qt.ini` (see below)

- Same goes for [Goggle recaptcha](https://www.google.com/recaptcha/admin) which is used to spot spam registrations. You'll need to add your site URL. Quran Tools currently only uses v3. For testing/local development, you'll need to add the URLs (preferably as a separate Google recaptcha site) as well. The password reset function relies on a Google ReCapthca key so this is not optional unless you edit the source code.

### (Optional) Create .htaccess file for 404 error redirects

If you are running Apache2, in order to redirect to a nice 404 page, it is recommended you use `/app/.htaccess.example` as a template for your own `.htaccess` file. Check the official documentation for Nginx and other webservers.

### qt.ini Configuration

Most of the config for Quran Tools is controlled by `library/config.php` which reads config variables from a file called `qt.ini`. It lives in the server root (but definitely not `/app` - so that it can not accidentally be displayed to users).

You will need to create a copy of `qt.ini` from `qt.ini.example` in the repo, and ensure that `qt.ini` is ignored in .gitignore as it contains sensitive credentials - all of which you should now know if you followed the above steps.

```ini
[Database Connection Credentials]

hostname        = 127.0.0.1          ; or localhost. Optionally includes the port number of the MySQL database host  eg 127.0.0.1:3306
mysqli_login    = "<mysql username>" ; e.g. qurantools
mysqli_password = "********"
database = "<mySQL database name>"   ; e.g. qurantools


[URLs and paths]

main_app_url       = https://qurantools.acme.org          ; public-facing URL for site. No trailing slash needed
privacy_policy_url = ;https://example.com/privacy-policy ; URL to a privacy policy - or comment it out
cookie_policy_url  = ;/cookie-policy                     ; URL to a cookie policy - or comment it out
license_path       = /licenses/license.php
terms_path         = /licenses/terms.php

[Email]

qt_admin_email  = admin@qurantools.acme.org ; used by email_qt() function
qt_developers[] = dev1@qurantools.acme.org  ; used by email_error_to_qt(). for multiple emails, just add extra lines
;qt_developers[] = dev2@qurantools.acme.org


[Google Stuff]

; see https://www.google.com/recaptcha/admin/site/
google_recaptcha_mode          = v3          ; 'v3' is used for password resets
google_recaptcha_site_key_v3   = xxxx        ; for invisible V3 reCAPTCHA
google_recaptcha_secret_key_v3 = xxxx        ; for invisible V3 reCAPTCHA
google_tag_manager_code        = GTM-*******


[Other]

is_user_registration_allowed = false    ; if true, then allow users to sign-up themselves. o/wise, users need to be manually created in the database
is_maintenance_mode_enabled  = false    ; if true, then everything usually redirects to maintenance.php
display_errors_locally       = false    ; only set to true for local development
mysql_error_reporting        = OFF      ; set to LOG on development only
minimum_full_name_length     = 7        ; combined character length of first & last name for consumer users
show_gdpr                    = true     ; shown register.php
gdpr_base_text               = 'By registering, you consent to receive occasional marketing and product related emails. Opt-out any time.' ; only shown if show_gdpr = true.
maximum_password_attempts    = 5
account_lock_time_minutes    = 15
password_reset_text          = 'PASSWORD HAS BEEN RESET'


[Branding]

; comment out the following lines to prevent branding appearing in the
; application
hosting_organisation     = 'University of X'
hosting_organisation_url = 'https://univerity.x.edu/'
```

These lines can also go in `config.php`, with suitable values selected:

```php
// used for displaying decent errors locally
ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);
```

### Test it works

You should now be able to visit a URL like <https://qurantool.acme.org/> and try to reset your password.

### How to deploy changes to the site

Assuming you are using git, then deploying changes to the server is just a matter of using the standard `git pull` commands. If you want to add pull hooks that rerun `composer install` (and `npm install` if working locally), then go ahead, but that is not part of this set-up.

You will probably need to know the passphrase for the private/public key that links your login on your server with your (GitHub) repo for the following Git commands.

To deploy to a server, ssh to that server and then:

```bash
> cd <application root>
> git fetch               # refresh repo with information about new branches
> git checkout <branch>   # checkout the branch (usually master) - and track it at origin if needs be
> git pull                # only necessary if the branch has been changed at origin
```

### Admin Levels

Users can be tagged in the database of users with one of three levels of admin rights:

| Level | Explanation |
| --- | --- |
| SUPERUSER | Can use all of the admin functions |
| ADMIN | Can use most of the admin functions, largely except the ones that rebuild the parsing/tagging databases |
| WORD_FIXER | Can only use the transliteration fix tools in the Admin menu |

### Development

Should you wish to make changes to Quran Tools, here are some exra notes:

1. CSS (from LESS) and Javascript are compiled and built using [Brunch](https://brunch.io/). While developing run `./node_modules/brunch/bin/brunch watch`

### Testing

QT uses Codeception to run tests. After installing `codeception` with `composer install`, the following commands can be used to run tests from the folder root - however, see the following notes before attempting these commands:

```sh
# run all tests with codeception
> ./vendor/bin/codecept run
# run all unit tests (very fast) with codeception
> ./vendor/bin/codecept run unit
# run all Phpbrowser acceptance tests (fast, no javascript)
> ./vendor/bin/codecept run acceptance_phpbrowser
# run all Webdriver acceptance tests (slower, javascript allowed)
> ./vendor/bin/codecept run acceptance_webdriver
> ./vendor/bin/codecept run tests/acceptance_webdriver    # alternative way of running
# run all tests in suite except those in the group named "needs_email_server"
> ./vendor/bin/codecept run tests/acceptance_phpbrowser --skip-group needs_email_server
```

As not all of the developers involved with QT wrote tests, automated testing of the application is rather sparse, and restricted mostly to login, registration and user management.

#### Testing Notes/Gotchas

- The first time you run codecept on a new install, some of the `_generated` files may need to be rebuilt. Do that with:

```bash
> ./vendor/bin/codecept build
```

- You'll need to create valid ReCaptcha keys for the URL of your testing machine so that the password reset webdriver tests work. Either that or figure out a way to mock this external dependency - which would lead to a faster test anyway.

- Ensure that a file called `test_config.yml` is in the same location as `qt.ini` on your testing machine. Ensure these keys exist (which closely match the contents of `qt.ini`):

```yml
main_app_url: http://some.qurantools.local/ # A local URL to hit for acceptance testing
mysql_host: 127.0.0.1 # Host that MySQL is running on
mysql_port: 3306 # Port that MySQL is listening on
db_name: some_database # MySQL database name, such as qurantools-dev
db_user: some_user # MySQL user, such as qurantools-dev
db_password: xxx # password for that MySQL user
```

- For historical reasons, during QT development, there was no separate testing database, so the tests ran against the development database - which means there is quite a bit of testing code written to delete test records added to the database. Codeception does support a reset/clean database function so this should be addressed at some stage. It does take quite a long time to import all the Qur'anic data into the database and recreate all the indexes - which is not an overhead that would be welcome in testing.

- When running acceptance tests with WebDriver (see `tests/acceptance_webdriver.suite.yml`), `ChromeDriver` needs to be installed and running on the local machine. See <https://codeception.com/docs/modules/WebDriver#ChromeDriver> for more information about installation and then how to execute it from the command line on your testing machine. This will likely require adding it to your path in a .bashrc (or similar): e.g. `export PATH=$PATH:~/Downloads/ChromeDriver`. Once done, run this: `chromedriver --url-base=/wd/hub`

- To test email sending, a ruby gem called `mailcatcher` needs to be installed and running locally. For more information, check <https://github.com/sj26/mailcatcher>. Getting `mailcatcher` to work properly can be tricky and requires installing ruby (probably with [rbenv](https://github.com/rbenv/rbenv) or rvm) as well as extra apache2 config in `php.ini` for both the webserver _and_ cli. You may also have problems with ASCII encoding with mailcatcher for testing. See <https://github.com/sj26/mailcatcher/issues/339>. The solution is to add `LANG=en_US.UTF-8` to the sendmail. Also check <https://gist.github.com/shark0der/28f55884a876f67c92ce>, <https://fostermade.co/blog/email-testing-for-development-using-mailcatcher> and <https://archive.block81.com/test-emails-locally-with-mailcatcher> for extra tips. A line like this should be in both `php.ini`'s:

```ini
;rvm style
sendmail_path = "LANG=en_US.UTF-8 /home/joe_bloggs/.rvm/gems/ruby-2.4.4/wrappers/catchmail --smtp-port 1025 -f qt_testing@my.computer"

;rbenv style
; sendmail_path = "LANG=en_US.UTF-8 /usr/bin/env/catchmail -f qt_testing@my.computer"
sendmail_path = "LANG=en_US.UTF-8 /home/joe_bloggs/.rbenv/shims/catchmail -f qt_testing@my.computer"
```

It configures your local PHP installation (webserver and cli) to use `mailcatcher` to fake the sending of mail. It may be a good idea to have `mailcatcher` run on system startup.
