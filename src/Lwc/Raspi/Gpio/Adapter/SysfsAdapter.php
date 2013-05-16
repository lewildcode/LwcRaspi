<?php

namespace Lwc\Raspi\Gpio\Adapter;

class SysfsAdapter implements AdapterInterface {
    
    const SYSFS_GPIO = '/sys/class/gpio';
    
    const SYSFS_EXPORT = '/sys/class/gpio/export';

    const SYSFS_UNEXPORT = '/sys/class/gpio/unexport';

    const SYSFS_DIRECTION = '/sys/class/gpio/gpio%d/direction';

    const SYSFS_VALUE = '/sys/class/gpio/gpio%d/value';
    
    const SYSFS_EXPORTFOLDER = '/sys/class/gpio/gpio%d';

    protected $pins = array();
    
    public function __construct() {
        if (!file_exists(self::SYSFS_GPIO)) {
            throw new \RuntimeException('Required sysfs folder not found: ' . self::SYSFS_GPIO);
        }
    }
    
    public function allocate($pin, $direction) {
        if ($this->isPrepared($pin)) {
            return;
        }
        if (!$this->isExported($pin)) {
            $this->export($pin);
        }
        file_put_contents(sprintf(self::SYSFS_DIRECTION, $pin), $direction);
        
        $this->pins[$pin] = array(
            'value' => fopen(sprintf(self::SYSFS_VALUE, $pin), 'r+')
        );
    }
    
    public function release($pin) {
        if (!$this->isPrepared($pin)) {
            return;
        }
        if ($this->isExported($pin)) {
            $this->unexport($pin);
        }
        unset($this->pins[$pin]);
    }
    
    public function isPrepared($pin) {
        return isset($this->pins[$pin]);
    }
    
    public function set($pin) {
        fwrite($this->pins[$pin]['value'], 1);
    }
    
    public function get($pin) {
        fseek($this->pins[$pin]['value'], 0);
        fread($this->pins[$pin]['value']);
    }
    
    public function clear($pin) {
        fwrite($this->pins[$pin]['value'], 0);
    }

    protected function export($pin) {
        file_put_contents(self::SYSFS_EXPORT, $pin);
    }
    
    protected function unexport($pin) {
        file_put_contents(self::SYSFS_UNEXPORT, $pin);
    }    
    protected function isExported($pin) {
        return file_exists(sprintf(self::SYSFS_EXPORTFOLDER, $pin));
    }
    
    /**
     * Unexport pins when instance is destructed
     */
    public function __destruct() {
        foreach(array_keys($this->pins) as $pin) {
            $this->unexport($pin);
        }
    }
}