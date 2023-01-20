# Jurager/Sender
[![Latest Stable Version](https://poser.pugx.org/jurager/sender/v/stable)](https://packagist.org/packages/jurager/sender)
[![Total Downloads](https://poser.pugx.org/jurager/sender/downloads)](https://packagist.org/packages/jurager/sender)
[![PHP Version Require](http://poser.pugx.org/jurager/sender/require/php)](https://packagist.org/packages/jurager/sender)
[![License](https://poser.pugx.org/jurager/sender/license)](https://packagist.org/packages/jurager/sender)

Sender - Laravel Provider for SMS-assistent.by


## Installation

``` bash
$ composer require jurager/sender
```

Add to config/app.php in section ```aliases```:

``` php
'Sender' => Jurager\Sender\Sender::class,
```

Publish package files by running 
```
php artisan vendor:publish --provider="Jurager\Sender\SenderServiceProvider"
```
## Usage

Now, if you have configured ```Queues```, you can create a ```Job``` like this below in ```/App/Http/Jobs```
```
<?php

namespace App\Jobs;

use Jurager\Sender\Sender;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SMS extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $to;
    protected $text;

    /**
     * @param $to
     * @param $text
     */
    public function __construct($to, $text)
    {
        $this->to   = $to;
        $this->text = $text;
    }

    public function handle(Sender $sender)
    {
        $sender->sendOne($this->to, $this->text);
    }
}
```

And after dispatch a new Job anywhere in your app
```
<?php

use App\Jobs\SMS;

class SampleController
{
    $this->dispatch((new SMS( '+71234567890', 'Hello world!')))->delay(5));
}
```

## License

This package is open-sourced software licensed under the [MIT license](LICENSE.md).
