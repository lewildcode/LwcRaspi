LwcRaspi
========

Raspberry Pi PHP library

## GPIO

For now, there is only the sysfs adapter, which uses linux sysfs exposed files/directories to interact with the GPIO interface.

    <?php
    use Lwc\Raspi\Gpio;
    
    $gpioAdapter = new Gpio\Adapter\SysfsAdapter();
    $pin = new Gpio\Pin(Gpio\Pin::PIN_CS, Gpio\Pin::DIRECTION_OUTPUT, $gpioAdapter));
    $pin->set();
    // ...and clear
    $pin->clear();
    
## SPI

To communicate with a periphal via SPI like an LCD controller, i've created a driveless solution for now using bitbanging.
As i already noticed, the combination of sysfs + bitbanging is way too slow for descent drawing performance on a display (and i have just a 128x64 one).

========

## Future

* Classes to interact with the [RaspiLCD](http://www.emsystech.de/produkt/raspi-lcd/)
* Other GPIO adapters
* Other SPI adapters (utilizing the Broadcom BCM 2835 via driver)
