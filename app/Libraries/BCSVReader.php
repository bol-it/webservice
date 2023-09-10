<?php

namespace App\Libraries;
use SplFileObject;
use SplFileInfo;
use Exception;

class BCSVReader
{
    const DEFAULT_ENCODING = 'UTF-8'; //
    protected $file = NULL; // Класс SplFileObject предоставляет объектно-ориентированный интерфейс для файла
    protected $info_file = NULL; // Класс SplFileInfo предлагает высокоуровневый объектно-ориентированный интерфейс к информации для отдельного файла
    protected $mode = 'r'; // Режим открытия файла
    protected $mode_list = ['r', 'r+', 'w', 'w+', 'a', 'a+', 'x', 'x+', 'c', 'c+']; // Список возможных режимов для чтения и записи
    protected $mode_list_write = ['r+', 'w', 'w+', 'a', 'a+', 'x', 'x+', 'c', 'c+']; // Список возможных режимов для записи
    protected $delimiter_list = array(";", ",", "|", "\t"); // допустимые разделители поля
    protected $enclosure_list = ['"', "'", ''];
    protected $delimiter = ';'; // Разделитель поля (только один однобайтовый символ)
    protected $enclosure = '"'; // Символ ограничителя поля (только один однобайтовый символ)
    protected $escape = '\\'; // Экранирующий символ (не более одного однобайтового символа). Пустая строка ("") отключает проприетарный механизм экранирования
    protected $input_encoding = 'UTF-8'; // Кодировка ввода
    protected $header_offset = NULL; // Смещение заголовков
    protected $offset_row = NULL; // С какой строки читать при отсутсвующем заголовке
    protected $header = []; // Заголовки в csv файле
    protected $count_header = 0; // Количество заголовков
    protected $duplicate_header = false; // Повторяющийся заголовок (true/false)
    protected $duplicate_header_list = []; // Список значения повторяющегося заголовка
    protected $append_suffix_header = 'header'; // Добавляемая строка при одинаковом заголовке

    public function __construct()
    {

    }

    /**
     * setInputEncoding
     *
     * @param  mixed $encoding
     * @return void
     */
    public function setInputEncoding($encoding = '')
    {
        if ($encoding) {
            if (is_string($encoding))
            $this->input_encoding = $encoding;
        }
        return $this;
    }

    /**
     * getDuplicateHeaderList
     *
     * @return void
     */
    public function getDuplicateHeaderList()
    {
        return $this->duplicate_header_list;
    }

    /**
     * getOffsetRow
     *
     * @return void
     */
    public function getOffsetRow()
    {
        return $this->offset_row;
    }

    /**
     * setHeaderOffset
     *
     * @param  mixed $header_offset
     * @return void
     */
    public function setOffsetRow($offset_row = NULL)
    {
        $offset_row = filter_var($offset_row, FILTER_VALIDATE_INT, array("options"=> array("min_range" => 0, "default" => NULL)));
        $this->offset_row = $offset_row;
        if (isset($this->offset_row)) $this->header_offset = NULL;
        return $this;
    }

    /**
     * getHeaderOffset
     *
     * @return void
     */
    public function getHeaderOffset()
    {
        return $this->header_offset;
    }

    /**
     * setHeaderOffset
     *
     * @param  mixed $header_offset
     * @return void
     */
    public function setHeaderOffset($header_offset = NULL)
    {
        $header_offset = filter_var($header_offset, FILTER_VALIDATE_INT, array("options"=> array("min_range" => 0, "default" => NULL)));
        $this->header_offset = $header_offset;
        if (isset($this->header_offset)) $this->offset_row = NULL;
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
        if (in_array($enclosure, $this->enclosure_list)) $this->enclosure = $enclosure;
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
        }
        return $this;
    }

    /**
     * convertToDefaultEncoding
     *
     * @param  mixed $val
     * @return void
     */
    protected function convertToDefaultEncoding($val) {
		return mb_convert_encoding($val, static::DEFAULT_ENCODING, $this->input_encoding);
	}

    /**
     * count
     *
     * @return void
     */
    public function count()
    {
        if (isset($this->file)) {
            $currentPosition = $this->file->key();
            $this->file->seek(PHP_INT_MAX);
            $linesTotal = $this->file->key();
            if (isset($this->header_offset)) {
                $linesTotal = $linesTotal - ($this->header_offset + 1);
            } else if (isset($this->offset_row)) {
                $linesTotal = $linesTotal - ($this->offset_row);
            }
            $this->file->seek($currentPosition);
            return $linesTotal;
        }
    }

    /**
     * renameHeader
     *
     * @param  mixed $header
     * @return void
     */
    protected function renameHeader($header = NULL)
    {
        if (isset($header)) {
            $this->duplicate_header_list = array_count_values($header);
            $filtered = array_filter($this->duplicate_header_list, function ($value, $key) {
                    return ($value > 1);
                },
                ARRAY_FILTER_USE_BOTH
            );
            $this->duplicate_header_list = $filtered;
            for ($i = 0; $i < count($header); $i++) {
                if (in_array($header[$i],array_keys($filtered))) {
                    $counts = array_count_values($header);
                    $numb = $filtered[$header[$i]] - $counts[$header[$i]] + 1;
                    $header[$i] = $header[$i].$this->append_suffix_header.$numb;
                }
            }
        }
        return $header;
    }

    /**
     * getHeader
     *
     * @return void
     */
    public function getHeader()
    {
        if (isset($this->header_offset)) {
            if (isset($this->file)) {
                $currentPosition = $this->file->key();
                $this->file->seek($this->header_offset);
                $this->header = $this->convertToDefaultEncoding($this->file->current());
                //dd($this->header);
                $header = array_unique(array_filter($this->header, 'is_string'));
                if ($this->header != $header) {
                    $this->duplicate_header = true;
                    $this->header = $this->renameHeader($this->header);
                    return $this->header;
                }
                $this->duplicate_header = false;
                $this->duplicate_header_list = [];
                $this->header = $header;
                return $this->header;
            }
            return [];
        }
        return [];
    }

    /**
     * readCSVToArrayObject
     *
     * @return void
     */
    public function readCSVToArrayObject()
    {
        $this->rewind();
        $rows = [];
        while (!$this->eof()) {
            $rows[] = (object) $this->readRowCSV();
        }
        return $rows;
    }

    /**
     * readCSVToArray
     *
     * @return void
     */
    public function readCSVToArray()
    {
        $this->rewind();
        $rows = [];
        while (!$this->eof()) {
            $rows[] = $this->readRowCSV();
        }
        return $rows;
    }

    /**
     * readRowCSV
     *
     * @return void
     */
    public function readRowCSV()
    {
        $row = [];
        if (isset($this->file)) {
            if (isset($this->header_offset)) {
                $row = array_combine($this->header, $this->convertToDefaultEncoding($this->file->current()));
                $this->file->next();
                return $row;
            }
            $row = $this->convertToDefaultEncoding($this->file->current());
            $this->file->next();
            return $row;
        }
        return $row;
    }

	/**
	 * rewind
	 *
	 * @return void
	 */
	public function rewind() {
        if (isset($this->file)) {
            if (isset($this->header_offset)) {
                $this->getHeader();
                return $this->file->seek($this->header_offset + 1);
            } else if (isset($this->offset_row)) {
                return $this->file->seek($this->offset_row);
            }
            return $this->file->rewind();
        }
	}

    /**
     * eof
     *
     * @return void
     */
    public function eof()
    {
        if (isset($this->file)) {
            return $this->file->eof();
        }
        return true;
    }

    /**
     * createFromFileObject
     *
     * @param  mixed $file
     * @return void
     */
    public function createFromFileObject($file = NULL)
    {
        if ((!is_null($file)) && ($file instanceof SplFileObject)) {
            $this->file = $file;
            $this->file->setFlags(SplFileObject::READ_CSV | SplFileObject::READ_AHEAD | SplFileObject::SKIP_EMPTY | SplFileObject::DROP_NEW_LINE);
            $this->file->setCsvControl($this->delimiter, $this->enclosure, $this->escape);
            return $this;
        }
        else {
            //файл не передан или не является объектом SplFileObject
            throw new Exception($file.' - не является файлом или SplFileObject');
        }
    }

    /**
     * createFromPath
     *
     * @param  mixed $path
     * @param  mixed $mode
     * @return void
     */
    public function createFromPath($path = NULL, $mode = 'r')
    {
        $path =  filter_var($path, FILTER_VALIDATE_REGEXP,  array( "options" => array('default' => NULL, 'regexp'=>"/^\/$|(\/[a-zA-Z_0-9-]+)*(.csv)$/")));
        if (in_array($mode, $this->mode_list)) $this->mode = $mode;
        if (!$path) throw new Exception('Путь к файлу пустой');
        $this->info_file = new SplFileInfo($path);
        if (!$this->info_file->isFile()) throw new Exception($path.' - не является файлом');
        if (!$this->info_file->isReadable()) throw new Exception($path.' - файл не доступен для чтения');
        if (in_array($mode, $this->mode_list_write)) {
            if (!$this->info_file->isWritable()) throw new Exception($path.' - файл не доступен для записи');
        }
        if ($this->info_file->getExtension() != 'csv') throw new Exception($path.' - не является csv');
        $this->file = new SplFileObject($path, $this->mode);
        $this->file->setFlags(SplFileObject::READ_CSV | SplFileObject::READ_AHEAD | SplFileObject::SKIP_EMPTY | SplFileObject::DROP_NEW_LINE);
        $this->file->setCsvControl($this->delimiter, $this->enclosure, $this->escape);
        return $this;
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
}
