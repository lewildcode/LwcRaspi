<?php
namespace Lwc\Raspi\Gpio;
use Lwc\Raspi\Gpio\Adapter\AdapterInterface;
class Pin
{
    const DIRECTION_INPUT = 'in';

    const DIRECTION_OUTPUT = 'out';

    const VALUE_ON = 1;

    const VALUE_OFF = 0;
    
    /**
     * Master out slave in
     * @var int gpio pin
     */
    const PIN_MOSI = 10;

    /**
     * serial clock
     * @var int gpio pin
     */
    const PIN_SCLK = 11;

    /**
     * chip select
     * @var int gpio pin
     */
    const PIN_CS = 8;

    /**
     * command/data selection
     * @var int gpio pin
     */
    const PIN_RS = 7;
    
    /**
     * reset
     * @var int gpio pin
     */
    const PIN_RST = 25;
    

    protected $pin;

    protected $direction;

    protected $adapter;

    public function __construct ($pin, $direction, AdapterInterface $adapter)
    {
        $this->pin = $pin;
        $this->direction = $direction;
        $this->adapter = $adapter;
        $adapter->allocate($pin, $direction);
    }
    
    public function get() {
        return $this->adapter->get($this->pin);
    }
    
    
    public function set() {
        $this->adapter->set($this->pin);
    }
    
    public function clear() {
        $this->adapter->clear($this->pin);
    }
    
    public function __destruct() {
        $this->adapter->release($this->pin);
    }
}