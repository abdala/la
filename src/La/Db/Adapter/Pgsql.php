<?php

class La_Db_Adapter_Pgsql extends Zend_Db_Adapter_Pdo_Pgsql
{
    public function getComments($tableName)
    {
        $sql = sprintf("SELECT c.table_schema, c.table_name,c.column_name, pgd.description
                        FROM pg_catalog.pg_statio_all_tables as st
                        JOIN pg_catalog.pg_description pgd ON (pgd.objoid = st.relid)
                        JOIN information_schema.columns c ON (pgd.objsubid = c.ordinal_position 
                                                          AND c.table_schema = st.schemaname 
                                                          AND c.table_name = st.relname)
                        WHERE c.table_name = '%s'", $tableName);
        $datas = $this->fetchAll($sql);
        $comments  = array();
        
        foreach ($datas as $data) {
            $comments[$data['column_name']] = trim($data['description']);
        }
        
        return $comments;
    }
    
    public function getReferences($schema, $table)
    {
        $data   = array();
        $sql    = "SELECT a.attname AS attribute,   
                          clf.relname AS table_ref,   
                          af.attname AS attribute_ref   
                    FROM pg_catalog.pg_attribute a   
                      JOIN pg_catalog.pg_class cl ON (a.attrelid = cl.oid AND cl.relkind = 'r')
                      JOIN pg_catalog.pg_namespace n ON (n.oid = cl.relnamespace)   
                      JOIN pg_catalog.pg_constraint ct ON (a.attrelid = ct.conrelid AND   
                           ct.confrelid != 0 AND ct.conkey[1] = a.attnum)   
                      JOIN pg_catalog.pg_class clf ON (ct.confrelid = clf.oid AND clf.relkind = 'r')
                      JOIN pg_catalog.pg_namespace nf ON (nf.oid = clf.relnamespace)   
                      JOIN pg_catalog.pg_attribute af ON (af.attrelid = ct.confrelid 
                                                      AND af.attnum = ct.confkey[1])
                    WHERE  cl.relname = '$table'";
        $stmt   = $this->query($sql);
        $result = $stmt->fetchAll();

        if ($result) {
            foreach ($result as $column) {
                $data[] = array('table'             => $column['table_ref'],
                                'reference_columns' => $column['attribute_ref'],
                                'columns'           => $column['attribute']);
            
            }
        }
    
        return $data;
    }

    public function getDependents($schema, $table, $field = 'id')
    {
        throw new Exception('Não foi implementado');
    }
}