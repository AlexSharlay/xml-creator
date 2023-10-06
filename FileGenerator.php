<?php

class FileGenerator
{
    /**
     * Количество строк в файле
     */
    const LINE_COUNT = 1000000;

    /**
     * Длина строки
     */
    const LINE_SIZE = 1000;

    private string $filePath;

    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
    }

    public function generate(string $fileName)
    {
        $file = fopen($this->filePath . '/' .$fileName, "w") or die("Невозможно открыть файл!");

        for ($i = 0; $i < self::LINE_COUNT; $i++)
        {
            fwrite($file, $this->generateRandomString() . PHP_EOL);
        }

        fclose($file);
    }

    private function generateRandomString($length = self::LINE_SIZE) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}