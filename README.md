# jurager/morphy

Laravel wrapper for phpMorphy library with PHP7 support

This library allow retireve follow morph information for any word:
- Base (normal) form
- All forms
- Grammatical (part of speech, grammems) information

## Install

Via Composer
``` bash
$ composer require jurager/morphy
```

## Usage
``` php
$morphy = new jurager\morphy\Morphy('en');
echo $morphy->getPseudoRoot('FIGHTY');
```
## Laravel support

Add to config/app.php:

Section ```providers```
``` php
jurager\morphy\MorphyServiceProvider::class,
```

Section ```aliases```
``` php
'Morphy'    => jurager\morphy\Facade\Morphy::class,
```

### Facade
``` php
Morphy::getPseudoRoot('БОЙЦОВЫЙ')
```

## Change log
Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing
Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## License
The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
