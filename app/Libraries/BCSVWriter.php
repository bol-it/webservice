<?php

namespace App\Libraries;
use SplFileObject;
use SplFileInfo;
use SplQueue;
use Exception;

class BCSVWriter
{
    const DEFAULT_ENCODING = 'UTF-8'; //
    protected $file = NULL; // Класс SplFileObject предоставляет объектно-ориентированный интерфейс для файла
    protected $info_file = NULL; // Класс SplFileInfo предлагает высокоуровневый объектно-ориентированный интерфейс к информации для отдельного файла
    protected $delimiter_list = array(";", ",", "|", "\t"); // допустимые разделители поля
    protected $enclosure_list = ['"', "'", ''];
    protected $delimiter = ';'; // Разделитель поля (только один однобайтовый символ)
    protected $enclosure = '"'; // Символ ограничителя поля (только один однобайтовый символ)
    protected $escape = '\\'; // Экранирующий символ (не более одного однобайтового символа). Пустая строка ("") отключает проприетарный механизм экранирования
    protected $outputEncoding = 'UTF-8'; // Кодировка вывода
    protected $header = NULL; // Заголовки в csv файле
    protected $max_length = 0; // Максимальная длина переданного массива для записи
    protected $array_check_of_length = true; //Проверка длины массива
    protected $queue_of_records = NULL; // Очередь записей
    protected $path = ''; // путь для сохранения файла
    protected $name = 'file.csv'; // имя файла для сохранения
    protected $array_records = []; // массив для записи

    public function __construct()
    {
        $this->file = new SplFileObject(tempnam(sys_get_temp_dir(), rand()).'.csv', 'w+');
        $this->file->setCsvControl($this->delimiter, $this->enclosure, $this->escape);
        $this->file->fwrite(chr(0xEF).chr(0xBB).chr(0xBF));
        $this->queue_of_records = new SplQueue();
    }

    /**
     * set_name
     *
     * @param  mixed $name
     * @return void
     */
    public function set_name($name = null)
    {
        $name =  filter_var($name, FILTER_VALIDATE_REGEXP,  array( "options" => array('default' => NULL, 'regexp'=>"/^([a-zA-Z_0-9-])+.csv/")));
        if (!is_null($name)) {
            $this->name = $name;
        }
        return $this;
    }

    /**
     * get_name
     *
     * @return void
     */
    public function get_name()
    {
        return $this->name;
    }

    /**
     * getPath
     *
     * @return void
     */
    public function getPath()
    {
        return $this->file->getPath();
    }

    /**
     * getFilename
     *
     * @return void
     */
    public function getFilename()
    {
        return $this->file->getFilename();
    }

    /**
     * getBasename
     *
     * @param  mixed $suffix
     * @return void
     */
    public function getBasename(string $suffix = "")
    {
        return $this->file->getBasename($suffix);
    }

    /**
     * get_max_length
     *
     * @return void
     */
    public function get_max_length()
    {
        return $this->max_length;
    }

    /**
     * setArrayCheckOfLength
     *
     * @param  mixed $array_check_of_length
     * @return void
     */
    public function setArrayCheckOfLength($array_check_of_length = true)
    {
        $array_check_of_length = filter_var($array_check_of_length, FILTER_VALIDATE_BOOLEAN);
        $this->array_check_of_length = $array_check_of_length;
        return $this;
    }

    /**
     * getArrayCheckOfLength
     *
     * @return void
     */
    public function getArrayCheckOfLength()
    {
        return $this->array_check_of_length;
    }

    /**
     * set_outputEncoding
     *
     * @param  mixed $encoding
     * @return void
     */
    public function set_outputEncoding($encoding = '')
    {
        if ($encoding) $this->outputEncoding = $encoding;
        $this->file->ftruncate(0);
        $this->file->rewind();
        if (mb_convert_case($this->outputEncoding, MB_CASE_UPPER, "UTF-8") == 'UTF-8') $this->file->fwrite(chr(0xEF).chr(0xBB).chr(0xBF));
        return $this;
    }


    /**
     * getEscape
     *
     * @return void
     */
    public function getEscape()
    {
        return $this->escape;
    }

    /**
     * setEscape
     *
     * @param  mixed $escape
     * @return void
     */
    public function setEscape($escape = '\\')
    {
        if (strlen($escape) == 1) {
            $this->escape = $escape;
            $this->file->setCsvControl($this->delimiter, $this->enclosure, $this->escape);
        }
        return $this;
    }

    /**
     * getEnclosure
     *
     * @return void
     */
    public function getEnclosure()
    {
        return $this->enclosure;
    }

    /**
     * setEnclosure
     *
     * @param  mixed $enclosure
     * @return void
     */
    public function setEnclosure($enclosure = '"')
    {
        if (in_array($enclosure, $this->enclosure_list)) {
            $this->enclosure = $enclosure;
            $this->file->setCsvControl($this->delimiter, $this->enclosure, $this->escape);
        }
        return $this;
    }

    /**
     * getDelimiter
     *
     * @return void
     */
    public function getDelimiter()
    {
        return $this->delimiter;
    }

    /**
     * setDelimiter
     *
     * @param  mixed $delimiter
     * @return void
     */
    public function setDelimiter($delimiter = ";")
    {
        if (in_array($delimiter, $this->delimiter_list)) $this->delimiter = $delimiter;
        if (strlen($delimiter) == 1) {
            if (!in_array($delimiter, $this->enclosure_list))
            $this->delimiter = $delimiter;
            $this->file->setCsvControl($this->delimiter, $this->enclosure, $this->escape);
        }
        return $this;
    }

    /**
     * convert_to_default_encoding
     *
     * @param  mixed $val
     * @return void
     */
    protected function convert_to_default_encoding($val) {
		return mb_convert_encoding($val, $this->outputEncoding, static::DEFAULT_ENCODING);
	}

    /**
     * getFile
     *
     * @return void
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * getHeader
     *
     * @return void
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * setHeader
     *
     * @param  mixed $header
     * @return void
     */
    public function setHeader($header = NULL)
    {
        if (!is_null($header)) {
            if (is_array($header)) {
                if ($this->array_check_of_length) {
                    if (count($header) > $this->max_length) $this->max_length = count($header);
                }
                $this->header = $header;
                if ($this->queue_of_records->isEmpty()) {
                    $this->queue_of_records->enqueue($header);
                }
                else {
                    unset($this->queue_of_records);
                    $this->queue_of_records = new SplQueue();
                    $this->queue_of_records->enqueue($header);
                }
                return $this;
            }
        }
        return $this;
    }

    public function setHeaderNoQueue($header = NULL)
    {
        if (!is_null($header)) {
            if (is_array($header)) {
                if ($this->array_check_of_length) {
                    if (count($header) > $this->max_length) $this->max_length = count($header);
                }
                $this->header = $header;
                $this->file->ftruncate(0);
                $this->file->rewind();
                $final_record = [];
                foreach ($this->header as $key => $value) {
                    if (array_key_exists($key, $this->header)) {
                        $final_record[] = $this->header[$key];
                    }
                    else {
                        $final_record[] = '';
                    }
                }
                $result = $this->file->fputcsv($final_record, $this->delimiter, $this->enclosure, $this->escape);
                if (!$result) throw new Exception('Ошибка записи в файл');
                return $this;
            }
        }
        return $this;
    }

    protected function is_multi($val = null)
    {
        if (!is_null($val)) {
            $rv = array_filter($val, 'is_array');
            if (count($rv) > 0) {
                foreach ($rv as $rv1) {
                    $rv2 = array_filter($rv1, 'is_array');
                    if (count($rv2) > 0) throw new Exception('Запись является трехмерным массивом');
                }
                return true;
            }
            $rv = array_filter($val, 'is_object');
            if (count($rv) > 0) return true;
            return false;
        }
        return false;
    }

    /**
     * max_count
     *
     * @param  mixed $val
     * @return void
     */
    protected function max_count($val = null)
    {
        if (!is_null($val)) {
            if (is_object($val)) {
                $ar = get_object_vars($val);
                return count($ar);
            }
            else if (is_array($val)) {
                return count($val);
            }
            else {
                throw new Exception('Запись не является массивом или объектом');
            }
        }
        return 0;
    }

    /**
     * add
     *
     * @param  mixed $val
     * @return void
     */
    public function add($val = NULL)
    {
        if (!is_null($val)) {
            if (is_array($val)) {
                if ($this->is_multi($val)) {
                    foreach ($val as $item_val) {
                        $this->queue_of_records->enqueue($item_val);
                        if ($this->array_check_of_length) {
                            $max_count = $this->max_count($item_val);
                            if ($max_count > $this->max_length) $this->max_length = $max_count;
                        }
                    }
                    if (!$this->array_check_of_length) $this->write_queue();
                    return $this;
                }
                $this->queue_of_records->enqueue($val);
                if ($this->array_check_of_length) {
                    $max_count = $this->max_count($val);
                    if ($max_count > $this->max_length) $this->max_length = $max_count;
                }
                else {
                    $this->write_queue();
                }
                return $this;
            }
            if (is_object($val)) {
                $this->queue_of_records->enqueue($val);
                if ($this->array_check_of_length) {
                    $max_count = $this->max_count($val);
                    if ($max_count > $this->max_length) $this->max_length = $max_count;
                }
                else {
                    $this->write_queue();
                }
                return $this;
            }
            throw new Exception('Запись не является массивом или объектом');
        }
        return $this;
    }

        /**
     * write_queue
     *
     * @return void
     */
    protected function writeNoQueue()
    {
        foreach ($this->array_records as $record) {
            if ((!is_array($record)) && (!is_object($record))) throw new Exception('Запись не является массивом или объектом');
            if (is_object($record)) $record = get_object_vars($record);
            $final_record = [];
            if (is_null($this->header)) {
                $final_record = array_values($record);
            }
            else {
                foreach ($this->header as $key => $value) {
                    if (array_key_exists($key, $record)) {
                        $final_record[] = $record[$key];
                    }
                    else {
                        $final_record[] = '';
                    }
                }
            }
            $final_record = $this->convert_to_default_encoding($final_record);
            $result = $this->file->fputcsv($final_record, $this->delimiter, $this->enclosure, $this->escape);
            if (!$result) throw new Exception('Ошибка записи в файл');
        }
    }

    /**
     * addNoQueue
     *
     * @param  mixed $val
     * @return void
     */
    public function addNoQueue($val = NULL)
    {
        if (!is_null($val)) {
            $this->array_records = [];
            if (is_array($val)) {
                if ($this->is_multi($val)) {
                    foreach ($val as $item_val) {
                        $this->array_records[] = $item_val;
                    }
                    $this->writeNoQueue();
                    return $this;
                }
                $this->array_records[] = $val;
                $this->writeNoQueue();
                return $this;
            }
            if (is_object($val)) {
                $this->array_records[] = $val;
                $this->writeNoQueue();
                return $this;
            }
            throw new Exception('Запись не является массивом или объектом');
        }
        return $this;
    }

    /**
     * download
     *
     * @return void
     */
    public function download()
    {
        if (!$this->queue_of_records->isEmpty()) $this->write_queue();
        $this->file->rewind();
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.$this->name.'"');
        header("Content-Length: " . $this->file->getSize());
        return $this->file->fpassthru();
    }

    /**
     * save_to_path
     *
     * @param  mixed $path
     * @return void
     */
    public function save_to_path($path = '')
    {
        $path =  filter_var($path, FILTER_VALIDATE_REGEXP,  array( "options" => array('default' => '', 'regexp'=>"/^\/$|(\/[a-zA-Z_0-9-]+)+$/")));
        if (strlen($path) > 1) {
            if (substr($path, -1, 1) != '/') $path .= '/';
        }
        else throw new Exception('Не выбран каталог для сохранения');
        $this->path = $path;
        if(!file_exists($this->path)) {
            if (!mkdir($this->path, 0777, true)) {
                throw new Exception('Не удалось создать каталог');
            }
        }
        if (!is_writable($this->path)) {
            throw new Exception('Указанный каталог не доступен для записи');
        }
        $from = $this->file->getRealPath();
        $to = $this->path.$this->name;
        //dd($this->file->getRealPath());
        if (!$this->queue_of_records->isEmpty()) $this->write_queue();
        if (copy($from, $to)) {
            unlink($from);
            return true;
        }
        return false;
    }

    /**
     * write_queue
     *
     * @return void
     */
    protected function write_queue()
    {
        while (!$this->queue_of_records->isEmpty()) {
            $record = $this->queue_of_records->dequeue();
            if ((!is_array($record)) && (!is_object($record))) throw new Exception('Запись не является массивом или объектом');
            if (is_object($record)) $record = get_object_vars($record);
            $final_record = [];
            if (is_null($this->header)) {
                $final_record = array_values($record);
            }
            else {
                foreach ($this->header as $key => $value) {
                    if (array_key_exists($key, $record)) {
                        $final_record[] = $record[$key];
                    }
                    else {
                        $final_record[] = '';
                    }
                }
            }
            if (($this->array_check_of_length) && (is_null($this->header))) {
                $len_final_record = count($final_record);
                if  ($len_final_record < $this->max_length) {
                    for ($i = $len_final_record; $i < $this->max_length; $i++) {
                        $final_record[] = '';
                    }
                }
            }
            $final_record = $this->convert_to_default_encoding($final_record);
            $result = $this->file->fputcsv($final_record, $this->delimiter, $this->enclosure, $this->escape);
            if (!$result) throw new Exception('Ошибка записи в файл');
        }
    }

    /**
     * save
     *
     * @return void
     */
    public function save()
    {
        $this->write_queue();
    }

}
