<?php
/**
 * Filter and Count Text in File
 * @author Riza Fauzi Rahman <riza.fauzi.rahman@gmail.com>
 * @since 2018.01.07
 */

class CountingFile
{
    const FIRST_COUNT = 1;

    /**
     * Path File
     * @var string
     */
    public $pathFile;

    /**
     * Total Files in Path
     * @var integer
     */
    public $totalFiles;

    /**
     * Flag
     * @var boolean
     */
    public $flag;

    /**
     * Id File
     * @var string[]
     */
    public $idFile = [];

    private $_conn;

    public function __construct() {
        $config = parse_ini_file("config.ini", true);
        $servername = $config['config']['servername'];
        $username = $config['config']['username'];
        $password = $config['config']['password'];
        $dbname = $config['config']['dbname'];

        $this->_conn = mysqli_connect($servername, $username, $password, $dbname);
        if ($this->_conn->connect_error) {
            die("Connection failed: " . $this->_conn->connect_error);
        }
    }

    public function printData()
    {
        if ($this->flag) {
            foreach ($this->idFile as $key => $id) {
                $this->_update($id, 'actionFlag', $this->flag);
            }
        }
        $selectData = $this->_select();
        foreach ($selectData as $key => $row) {
            $counter[$key] = $row['counter'];
        }
        array_multisort($counter, SORT_DESC, $selectData);
        $result = $selectData['0'];
        if ($result) {
            echo $result['content'] . ' ' . $result['counter'];
        }
        return null;
    }

    public function mapingData()
    {
        $filestring = file_get_contents($this->pathFile);
        $selectData = $this->_select(null, $filestring);
        if ($selectData == false) {
            return $this->_insert($filestring, self::FIRST_COUNT, $this->totalFiles);
        } else {
            $this->_filterAndCount($selectData, $filestring);
            return null;
        }
    }

    private function _filterAndCount($selectData, $filestring)
    {
        if($selectData['totalFile'] !== $this->totalFiles) {
            $this->_update($selectData['id'], 'totalFile', $this->totalFiles);

        }
        if ($selectData['content'] == $filestring && $selectData['actionFlag'] == 0 ||
            $selectData['totalFile'] != $this->totalFiles) {
            $this->_update($selectData['id'], 'counter', $selectData['counter'] + self::FIRST_COUNT);
        }
    }

    private function _insert($content, $counter, $totalFiles)
    {
        $sql = 'insert into directoryList(content, counter, totalFile)values("' .
            $content . '", ' . $counter . ', ' . $totalFiles . ')';
        $this->_conn->query($sql);
        return mysqli_insert_id($this->_conn);
    }

    private function _update($id, $coloumn, $value)
    {
        $sql = 'update directoryList set '. $coloumn . ' = ' . $value . ' where id = '. $id;
        $result = $this->_conn->query($sql);
        return $result;
    }

    private function _select($id = null, $content = null)
    {
        $sql = 'select id, content, counter, totalFile, actionFlag from directoryList';
        if ($id) {
            $sql .= ' where id = ' . $id . ' limit 1';
        } elseif ($content) {
            $sql .= ' where content = "' . $content . '" limit 1';
        }
        $result = $this->_conn->query($sql);
        if ($result && $id || $result && $content) {
            return mysqli_fetch_assoc($result);
        } else {
            return mysqli_fetch_all($result, MYSQLI_ASSOC);
        }
        return false;
    }
}
