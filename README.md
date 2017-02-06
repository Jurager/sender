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
Jurager\Sender\SenderServiceProvider::class,
```

Section ```aliases```
``` php
'Sender'    => Jurager\Sender\Sender::class,
```
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


## Change log
Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing
Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## License
The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
