<?php
namespace Lwc\Raspi\Spi\Adapter;
use Lwc\Raspi\Gpio\Pin;
use Lwc\Raspi\Spi\Adapter\AdapterInterface;

class GpioBitbangingAdapter implements AdapterInterface
{

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
     * @var unknown
     */
    const PIN_RS = 7;

    /**
     * reset
     * @var unknown
     */
    const PIN_RST = 25;

    const MODE_DATA = 'data';

    const MODE_COMMAND = 'command';

    public function __construct (Pin $mosi, Pin $sclk, Pin $cs, Pin $rs)
    {
        $this->mosi = $mosi;
        $this->sclk = $sclk;
        $this->cs = $cs;
        $this->rs = $rs;
    }

    public function writeCommand ($data)
    {
        $this->beginTransmission();
        $this->setMode(self::MODE_COMMAND);
        $this->sendSerialData($data);
        $this->endTransmission();
    }

    public function writeData ($data)
    {
        $this->beginTransmission();
        $this->setMode(self::MODE_DATA);
        $this->sendSerialData($data);
        $this->endTransmission();
    }

    protected function beginTransmission ()
    {
        $this->cs->clear();
    }

    protected function endTransmission ()
    {
        $this->cs->set();
    }

    protected function setMode ($mode)
    {
        if ($mode == self::MODE_DATA) {
            $this->cs->set();
        } else {
            $this->cs->clear();
        }
    }

    protected function sendSerialData ($data)
    {
        for ($i = 0; $i < 8; $i ++) {
            
            // consider leftmost bit, set mosi if bit is 1, clear mosi if bit is 0
            if ($data & 0x80) {
                $this->mosi->set();
            } else {
                $this->mosi->clear();
            }
            
            $data <<= 1; // shift byte left, so next bit will be leftmost
            

            // pulse clock to indicate that bit value should be read
            $this->sclk->clear();
            $this->sclk->set();
        }
    }
}