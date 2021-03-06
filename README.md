# laravel-verbose
[![Latest Stable Version](https://poser.pugx.org/dusterio/laravel-verbose/v/stable)](https://packagist.org/packages/dusterio/laravel-verbose)
[![Total Downloads](https://poser.pugx.org/dusterio/laravel-verbose/downloads)](https://packagist.org/packages/dusterio/laravel-verbose)
[![License](https://poser.pugx.org/dusterio/laravel-verbose/license)](https://packagist.org/packages/dusterio/laravel-verbose)

Package that adds verbosity to Laravel/Lumen built-in console commands

![Laravel Verbose in action](https://www.mysenko.com/images/laravel-verbose2.gif)

## Overview
Even though Laravel console commands have verbosity flags `-v/-vv/-vvv` they are actually ignored. Therefore, using some of the console commands leaves developer completely blind – you have no idea what's happening behind the scenes.

Example running `queue:work` without this package:

```bash
$ php artisan queue:work --once
$
```

What? Did it work or not? Was there a job in the queue or not? Why did it take several seconds to complete - does it mean it actually worked? What queue or connection did it use because I don't remember if I set them correctly? 

Reminds of Microsoft products uh? :)

Example running `queue:work` after installing this package:

```bash
$ php artisan queue:work -vv
Using connection: sqs
Using queue: live-visits
The queue seems to be empty.
Sleeping for 3 seconds.
```

Let's now break AWS credentials so that SQS is unreachable:

```bash
$ php artisan queue:work -vv
Using connection: sqs
Using queue: live-visits
Error executing "ReceiveMessage" on "https://sqs.ap-southeast-2.amazonaws.com/XXX/live-visits"; AWS HTTP error...
Couldn't fetch a job from the queue. See the log file for more information.
```

Voilà! Now I know exactly what happened in both cases, and now `-vv` flag did exactly what it was supposed to do.

## Installation

```bash
$ composer require dusterio/laravel-verbose
```

Then add a service provider to your `config/app.php`:

```php
  /* ... */
  Dusterio\LaravelVerbose\Integrations\LaravelServiceProvider::class,
  /* ... */
```

## Compatibility

So far tested with:

- Laravel 5.4
- Laravel 5.3 (`queue:work` only at the moment)
- Lumen 5.4

## Currently supported (read extended) commands

- `queue:work`
- `queue:listen`

