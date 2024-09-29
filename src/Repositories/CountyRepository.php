<?php
namespace App\Repositories;

class CountyRepository extends BaseRepository
{

    function __construct($host = self::HOST, $user = self::USER, $password = self::PASSWORD, $database = self::DATABASE)
    {
        parent::__construct($host, $user, $password, $database);
        $this->tableName = 'counties';
    }

    // public function update($id, array $data){
    //     $set = '';
    //     foreach($data as $field => $value){
    //         if($set > ''){
    //             $set .= ", $field = '$value'";
    //         }else{
    //             $set .= "$field = '$value'";
    //         }
    //     }

    //     $query = "UPDATE `{$this->tableName}` SET %s WHERE id = $id";
    //     $query = sprintf($query, $set);
    //     $this->mysqli->query($query);
        
    //     return $this->find($id);
    // }
}


//TODO: felülírni orderby-jal

// public function getAll(): array
// {
//     $query = $this->select();

//     // ". ORDER BY name";
//     return $this->mysqli->query($query)->fetch_all(MYSQLI_ASSOC);
// }
