<?php

class La_Db_Adapter_Mysql extends Zend_Db_Adapter_Pdo_Mysql
{
    public function getComments($tableName)
    {
        $sql = sprintf("SHOW FULL COLUMNS FROM `%s`;", $tableName);
        $datas = $this->fetchAll($sql);
        $comments  = array();
        
        foreach ($datas as $data) {
            $comment = trim($data['Comment']);
            
            if ($comment) {
                $comments[$data['Field']] = $comment;
            } else {
                $comment = Zend_Filter::filterStatic($data['Field'], 'Word_UnderscoreToSeparator');
                $comments[$data['Field']] = ucfirst($comment);
            }
        }
        
        return $comments;
    }
    
    public function getReferences($schema, $table)
    {
        $data   = array();
        $sql    = "SHOW CREATE TABLE `$table`";
        $stmt   = $this->query($sql);
        $result = $stmt->fetchAll();
        $values = explode("CONSTRAINT", $result[0]["Create Table"]);

        if (count($values) > 1) {
            for ($i = 1; $i < count($values); $i++) {
                $foreign       = explode("FOREIGN KEY", $values[$i]);
                $foreignData   = explode("`", $foreign[1]);
                $references    = explode("REFERENCES", $foreign[1]);
                $referenceData = explode("`", $references[1]);

                $class = ucfirst($referenceData[1]);
                
                $data[] = array('table'             => $referenceData[1],
                                'reference_columns' => $referenceData[3],
                                'columns'           => $foreignData[1]);
            }
        }
        
        return $data;
    }

    public function getDependents($schema, $table, $field = 'id')
    {
        $data   = array();
        $tables = $this->listTables();
        
        foreach($tables as $value) {
            $sql    = "SHOW CREATE TABLE `$value`";
            $stmt   = $this->query($sql);
            $result = $stmt->fetchAll();
            $values = explode("CONSTRAINT", $result[0]["Create Table"]);

            if (count($values) > 1) {
                for ($i = 1; $i < count($values); $i++) {
                    $foreign       = explode("FOREIGN KEY", $values[$i]);
                    $foreignData   = explode("`", $foreign[1]);
                    $references    = explode("REFERENCES", $foreign[1]);
                    $referenceData = explode("`", $references[1]);

                    if ($referenceData[1] == $table && $referenceData[3] == $field) {
                        $data[] = array('table'  => $value,
                                        'column' => $foreignData[1],
                                        'schema' => null);
                    }
                }
            }
        }
        return $data;
    }
}