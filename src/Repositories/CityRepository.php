<?php
namespace App\Repositories;

class CityRepository extends BaseRepository
{

    function __construct($host = self::HOST, $user = self::USER, $password = self::PASSWORD, $database = self::DATABASE)
    {
        parent::__construct($host, $user, $password, $database);
        $this->tableName = 'cities';
    }

    public function update($id, array $data){
        $set = '';
        foreach($data as $field => $value){
            if($set > ''){
                $set .= ", $field = '$value'";
            }else{
                $set .= "$field = '$value'";
            }
        }

        $query = "UPDATE `{$this->tableName}` SET %s WHERE id = $id";
        $query = sprintf($query, $set);
        $this->mysqli->query($query);
        
        return $this->find($id);
    }

    public function getCities($countyId){
        // $sql = $this->select()."INNER JOIN counties ON cities.id_county = counties.id WHERE counties.id = {$countyId}";
        return $this->mysqli->query("SELECT cities.id, cities.zip_code, cities.city FROM cities INNER JOIN counties ON cities.id_county = counties.id WHERE counties.id = {$countyId}")->fetch_all(MYSQLI_ASSOC);
        // return $this->mysqli->query($sql)->fetch_all(MYSQLI_ASSOC);
    }

    public function getCity($id, $countyId){
        $result = $this->mysqli->query("SELECT cities.id, cities.zip_code, cities.city FROM cities INNER JOIN counties ON cities.id_county = counties.id WHERE counties.id = {$countyId} AND cities.id = {$id}")->fetch_assoc();
        if(!$result){
            $result = [];
        }
        return $result;
   
    }

   
}