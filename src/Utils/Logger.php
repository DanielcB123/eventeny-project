<?php

namespace App\Utils;

class Logger
{
    private static $logFile;
    
    private static function init()
    {
        if (self::$logFile !== null) {
            return;
        }
        
        $logDir = __DIR__ . '/../../logs';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        self::$logFile = $logDir . '/error.log';
    }
    
    private static function write($level, $message, $context = [])
    {
        self::init();
        
        $timestamp = date('Y-m-d H:i:s');
        $contextStr = !empty($context) ? ' | Context: ' . json_encode($context) : '';
        $logMessage = "[{$timestamp}] {$level}: {$message}{$contextStr}" . PHP_EOL;
        
        file_put_contents(self::$logFile, $logMessage, FILE_APPEND);
    }
    
    public static function error($message, $context = [])
    {
        self::write('ERROR', $message, $context);
    }
    
    public static function warning($message, $context = [])
    {
        self::write('WARNING', $message, $context);
    }
    
    public static function info($message, $context = [])
    {
        self::write('INFO', $message, $context);
    }
    
    public static function exception(\Exception $e, $context = [])
    {
        self::init();
        
        $timestamp = date('Y-m-d H:i:s');
        $contextStr = !empty($context) ? ' | Context: ' . json_encode($context) : '';
        $logMessage = "[{$timestamp}] EXCEPTION: {$e->getMessage()}" . PHP_EOL;
        $logMessage .= "File: {$e->getFile()}:{$e->getLine()}" . PHP_EOL;
        $logMessage .= "Stack trace:" . PHP_EOL . $e->getTraceAsString() . PHP_EOL;
        $logMessage .= $contextStr . PHP_EOL;
        
        file_put_contents(self::$logFile, $logMessage, FILE_APPEND);
    }
}


