<?php
namespace Lwc\Raspi\RaspiLcd;

use Lwc\Raspi\Spi\Adapter\AdapterInterface as SpiAdapterInterface;
use \Lwc\Raspi\Gpio\Pin as GpioPin;

class Display
{

    const LCD_X_OFFSET = 4;

    protected $backlight;

    protected $framebuffer;
    
    protected $width = 128;
    
    protected $height = 64;
    
    protected $spiAdapter;

    public function __construct (SpiAdapterInterface $spiAdapter, GpioPin $backlightPin, GpioPin $resetPin)
    {
        $this->spiAdapter = $spiAdapter;
        $this->backlight = $backlightPin;
        $this->reset = $resetPin;
        
        $this->framebuffer = new Framebuffer($this->width, $this->height);
        $this->enableBacklight();
        $this->init();
        
        for($i = 0; $i < 50; $i++) {
            $this->framebuffer->set(0, $i);
        }
        $this->writeFramebuffer();
    }

    public function enableBacklight ()
    {
        $this->backlight->set();
    }

    public function disableBacklight ()
    {
        $this->backlight->clear();
    }

    public function isBacklightEnabled ()
    {
        return $this->backlight->get();
    }

    public function setContrast($contrast) {
        if ($contrast < 0 || $contrast > 63) {
            throw new \OutOfRangeException('Contrast must be between 0..63');
        }
        
        $this->spiAdapter->writeCommand(0x81);
        $this->spiAdapter->writeCommand($contrast);
    }
    
    protected function init ()
    {
        $this->reset->clear();
        usleep(50);
        $this->reset->set();
        usleep(200);
        
        $initCommands = array(0xE2, 0x40, 0xA1, 0xC0, 0xA4, 0xA6, 0xA2, 0x2F, 0x27, 0x81, 8, 0xFA, 0x90, 0xAF);
        
        foreach($initCommands as $command) {
            $this->spiAdapter->writeCommand($command);
        }
        $this->writeFramebuffer();
    }

    protected function setXy ($x, $y)
    {
        $x += self::LCD_X_OFFSET;
        $this->spiAdapter->writeCommand(0x00 + ($x & 0x0F));
        $this->spiAdapter->writeCommand(0x10 + (($x >> 4) & 0x0F));
        $this->spiAdapter->writeCommand(0xB0 + ($y & 0x07));
    }

    protected function writeFramebuffer ()
    {
        for ($i = 0; $i < 8; $i ++) {
            $this->setXy(0, $i);
            for ($x = 0; $x < $this->width; $x ++) {
                $this->spiAdapter->writeData($this->framebuffer->get($x, $i));
            }
        }
    }
}