<?php

class XmlConverter
{
    /**
     * Минимальный размер входного файла в мегабайтах
     */
    const MIN_FILE_SIZE = 10;

    /**
     * Минимальный размер входного файла в мегабайтах
     */
    const MAX_FILE_SIZE = 4000;

    /**
     * Размер сгенерированного id
     */
    const ID_SIZE = 16;

    /**
     * Входной файл
     *
     * @var string
     */
    private string $inputFilePath;

    /**
     * Выходной файл
     *
     * @var string
     */
    private string $outputFilePath;

    private XMLWriter $xmlWriter;

    public function __construct(string $inputFilePath, string $outputFilePath)
    {
        if (!file_exists($inputFilePath)){
            throw new InvalidArgumentException('Файл отсуствует');
        }

        //Размер файла в мегабайтах
        $filesize = number_format(filesize($inputFilePath) / 1048576, 2);

        if ($filesize < self::MIN_FILE_SIZE){
            throw new LengthException('Минимальный размер файла: '  . self::MIN_FILE_SIZE . ' МБ');
        }

        if ($filesize > self::MAX_FILE_SIZE){
            throw new LengthException('Максимальный размер файла: '  . self::MIN_FILE_SIZE . ' МБ');
        }

        $this->inputFilePath = $inputFilePath;
        $this->outputFilePath = $outputFilePath;

        $this->xmlWriter = new XMLWriter();
    }

    /**
     * Функция построчно читает txt файл и записывает в xml
     * При записи в xml файл буфер сбрасывается через каждую 1000 строк
     *
     * @return bool
     * @throws Exception
     */
    public function convert(): bool
    {
        $text = fopen($this->inputFilePath, "r");

        if ($text === false) {
            throw new RuntimeException('Ошибка при открытии');
        }

        $this->startXmlDocument();
        $i = 0;
        while (($buffer = fgets($text)) !== false) {
            $this->xmlWriter->text($buffer);
            $i++;
            if (0 == $i % 1000) {
                file_put_contents($this->outputFilePath, $this->xmlWriter->flush(), FILE_APPEND);
                $i = 0;
            }
        }

        $this->endXmlDocument();

        fclose($text);

        return true;
    }

    /**
     * Создание и начало записи xml документа,
     * Формирование базовых элементов
     *
     * @return void
     * @throws Exception
     */
    private function startXmlDocument(): void
    {
        $this->xmlWriter->openMemory();
        $this->xmlWriter->startDocument('1.0', 'UTF-8');

        $this->xmlWriter->startElement('root');

        $this->xmlWriter->writeElement('serverType', 'server');

        $this->xmlWriter->startElement('entry');

        $this->xmlWriter->writeElement('id', $this->generateRandomString(self::ID_SIZE));
        $this->xmlWriter->writeElement('type', 'text');
        $this->xmlWriter->writeElement('name', basename($this->outputFilePath));

        $this->xmlWriter->startElement('text');
    }

    /**
     * Завершение записи в xml документ
     *
     * @return void
     */
    private function endXmlDocument(): void
    {
        $this->xmlWriter->endElement();
        $this->xmlWriter->endElement();
        $this->xmlWriter->endElement();

        file_put_contents($this->outputFilePath, $this->xmlWriter->flush(), FILE_APPEND);
    }

    /**
     * Используется для генерации рандомного id
     *
     * @param int $length
     * @return string
     * @throws Exception
     */
    private function generateRandomString(int $length = 16): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ ';
        $charactersLength = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }

        return $randomString;
    }
}