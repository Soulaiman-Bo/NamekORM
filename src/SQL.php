<?php


class SQL
{

    private $connection;
    private $tableName;
    private $queryStatment;
    private $currentStatmentType;

    public function __construct(string $connection)
    {
        $this->connection = $connection;
        return $this;
    }

    public function select($ArrayOfFields = ["*"])
    {

        $this->currentStatmentType = "select";
        $this->queryStatment = "SELECT ";
        
        for ($i = 0; $i < count($ArrayOfFields); $i++) {
            $this->queryStatment .= $ArrayOfFields[$i] . ", ";
        }

        $this->queryStatment = rtrim($this->queryStatment, ', ');

        return $this;
    }

    public function from($tableName)
    {
        $this->tableName = $tableName;

        $this->queryStatment .=  " FROM " . $this->tableName;

        return $this;
    }

    public function where(...$ArrayOfFields)
    {
    }

    public function save()
    {
        // $query = $this->connection->prepare($this->queryStatment);
        // $query->execute();
        // $result = $query->get_result();
        // $query->close();
        // return $result;

        echo $this->queryStatment;
    }

}


$sql = new SQL("connection");

$sql->select()->from("users")->save();
