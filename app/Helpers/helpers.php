<?php

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

/**
 * [storeCustomLogsThrowable description]
 * @param  mixed  $data  [data to be logged]
 * @param  string        $fileName    [custom file name]
 * @return void          [no return]
 */
function storeCustomLogsThrowable($data, $fileName, $level = 'debug', $maxFileSizeMB = 10)
{
    $data = [
        $data->getMessage(),
        $data->getFile(),
        $data->getLine(),
        $data->getTrace()
    ];

    storeCustomLogs($data, $fileName, $level, $maxFileSizeMB);
}

/**
 * [storeCustomLogs description]
 * @param  mixed  $data  [data to be logged]
 * @param  string        $fileName    [custom file name]
 * @return void          [no return]
 */
function storeCustomLogs($data, $fileName, $level = 'debug', $maxFileSizeMB = 10, $type = '')
{
    if ($type == 'th') {
        $data = [
            $data->getMessage(),
            $data->getFile(),
            $data->getLine(),
            $data->getTrace()
        ];
    }

    // Map log level to Logger constants
    $logLevels = [
        'debug' => Logger::DEBUG,
        'info' => Logger::INFO,
        'notice' => Logger::NOTICE,
        'warning' => Logger::WARNING,
        'error' => Logger::ERROR,
        'critical' => Logger::CRITICAL,
        'alert' => Logger::ALERT,
        'emergency' => Logger::EMERGENCY,
    ];

    //Define the file path and the max file size (in bytes)
    $logPath = storage_path('logs/' . $fileName . '.log');
    $maxFileSize = $maxFileSizeMB * 1024 * 1024; // Convert MB to bytes

    $logDirectory = dirname($logPath);

    // Check if the directory exists
    if (!is_dir($logDirectory)) {
        // Create the directory and all necessary parent directories
        mkdir($logDirectory, 0777, true);
    }

    // Ensure the directory is writable
    if (!is_writable($logDirectory)) {
        chmod($logDirectory, 0777);
    }

    // Check if the log file exists and if its size exceeds the limit
    if (File::exists($logPath) && File::size($logPath) >= $maxFileSize) {
        // Rename the existing log file with a timestamp to rotate
        $newLogPath = storage_path('logs/' . $fileName . '_' . now()->format('Y-m-d_H-i-s') . '.log');
        File::move($logPath, $newLogPath);
    }

    // Build the logger with a StreamHandler
    Log::build(config: [
        'driver' => 'custom',
        'via' => function () use ($logPath) {
            $logger = new Logger('custom_logger');
            $logger->pushHandler(new StreamHandler($logPath, Logger::DEBUG, true, 0777));
            return $logger;
        }
    ])->debug($logLevels[$level], $data);
}