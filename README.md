# jurager/sender

Sender - Laravel Provider for SMS-assistent.by


## Install

Via Composer
``` bash
$ composer require jurager/sender
```

## Laravel support

Add to config/app.php:

Section ```providers```
``` php
jurager\sender\SenderServiceProvider::class,
```

Section ```aliases```
``` php
'Sender'    => jurager\sender\Sender::class,
```


## Change log
Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing
Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## License
The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
