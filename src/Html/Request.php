<?php

namespace App\Html;

use App\Repositories\CountyRepository;
use App\Repositories\CityRepository;

class Request
{
    static function handle()
    {
        switch ($_SERVER["REQUEST_METHOD"]) {
            case "POST":
                self::postRequest();
                break;
            case "GET":
                self::getRequest();
                break;
            case "PUT":
                self::putRequest();
                break;
            case "DELETE":
                self::deleteRequest();
                break;
            default:
                echo 'Unknown request type';
                break;
        }
    }

    public static function getRequest()
    {
        // $uri = $_SERVER['REQUEST_URI'];

        $uri = self::getResourceName($_SERVER['REQUEST_URI']);
        switch ($uri) {
            case 'counties':
                $repo = new CountyRepository();
                $id = self::getResourceId($_SERVER['REQUEST_URI']);
                if ($id) {
                    $entities = $repo->getById($id);
                } else {
                    $entities = $repo->getAll();
                }

                $code = 200;
                if (empty($entities)) {
                    $code = 404;
                    Response::response([], $code);
                    return;
                }
                Response::response($entities, $code);
                break;
            case 'cities':
                $repo = new CityRepository();
                $id = self::getResourceId($_SERVER['REQUEST_URI']);
                $countyId = 0;
                if ($id) {
                    $URIarr = explode("/", $_SERVER["REQUEST_URI"]);
                    $countyURI = array_splice($URIarr, 1, 2);
                    if($countyURI && $countyURI[0] == "counties"){
                        $countyId = self::getResourceId(implode("/", $countyURI));
                    }
                    else{
                        $code = 404;
                        Response::response([], 404, "$uri not found");
                        return;
                    }
                    $entities = $repo->getCity($id, $countyId);
                } else {
                    $URIarr = explode("/", $_SERVER["REQUEST_URI"]);
                    $countyURI = array_splice($URIarr, 1, 2);
                    if($countyURI && $countyURI[0] == "counties"){
                        $countyId = self::getResourceId(implode("/", $countyURI));
                    }
                    else{
                        $code = 404;
                        Response::response([], 404, "$uri not found");
                        return;
                    }
                    $entities = $repo->getCities($countyId);
                }

                $code = 200;
                if (empty($entities)) {
                    $code = 404;
                }
                Response::response($entities, $code);
                break;
            default:
                Response::response([], 404, "$uri not found");
                break;

        }
    }

    //azért hogy DRY legyen, nem tudok még rá értelmes nevet
    // private static function manageRequests($repo)
    // {
    //     $id = self::getResourceId($_SERVER['REQUEST_URI']);
    //     if ($id) {
    //         $entities = $repo->getById($id);
    //     } else {
    //         $entities = $repo->getAll();
    //     }

    //     $code = 200;
    //     if (empty($entities)) {
    //         $code = 404;
    //     }
    //     Response::response($entities, $code);
    // }

    private static function deleteRequest()
    {
        $id = self::getResourceId($_SERVER['REQUEST_URI']);
        if (!$id) {
            Response::response([], 400, Response::STATUSES[400]);
            return;
        }
        $resourceName = self::getResourceName($_SERVER['REQUEST_URI']);
        switch ($resourceName) {
            case 'counties':
                $code = 404;
                $db = new CountyRepository();
                $result = $db->deleteById($id);
                if ($result) {
                    Response::response([], 200);
                } else {
                    Response::response([], 404);
                }
                break;
            case 'cities':
                $code = 404;
                $db = new CityRepository();
                $result = $db->deleteById($id);
                if ($result) {
                    Response::response([], 200);
                } else {
                    Response::response([], 404);
                }
                break;
        }
        return;
    }

    private static function postRequest(){
        $resourceName = self::getResourceName($_SERVER['REQUEST_URI']);
        switch ($resourceName) {
            case 'counties':
                $data=self::getRequestData();
                if(isset($data['name'])){
                    $db=new CountyRepository();
                    $newId=$db->create($data);
                    $code=201;
                    if(!$newId){
                        $code=400;
                    }
                }
                Response::response(['id'=>$newId], $code);
                break;
            case 'cities':
                $repo = new CityRepository();
                $id = self::getResourceId($_SERVER['REQUEST_URI']);
                $countyId = 0;
                
                $URIarr = explode("/", $_SERVER["REQUEST_URI"]);
                $countyURI = array_splice($URIarr, 1, 2);
                $code = 200;
                if($countyURI && $countyURI[0] == "counties"){
                    $countyId = self::getResourceId(implode("/", $countyURI));
                    $data = self::getRequestData();
                    if(isset($data["city"])){
                        $newId = $repo->create($data);
                        $code = 201;
                        if(!$newId){
                            $code = 400;
                            Response::response([], $code);
                            return;
                        }
                        Response::response(['id'=>$newId], $code);
                    }else{
                        $code = 400;

                        Response::response([], $code);
                    }
                }
                else{
                    $code = 404;
                    Response::response([], 404, "$uri not found");
                    return;
                }
                break;
        }
    }


    private static function putRequest(){
        $id = self::getResourceId($_SERVER['REQUEST_URI']);
        if(!$id){
            Response::response([], 400, Response::STATUSES[400]);
            return;
        }
        $resourceName = self::getResourceName($_SERVER["REQUEST_URI"]);
        switch ($resourceName) {
            case 'counties':
                $data = self::getRequestData();
                
                $db = new CountyRepository();
                $entity = $db->find($id);
                $code = 404;
                if($entity){
                    $result = $db->upDate($id, ["name" => $data["name"]]);
                    if($result){
                        $code = 200;
                    }
                }
                    
                
                Response::response($result, $code);
                break;
            case "cities":
                $data = self::getRequestData();
                $db = new CityRepository();
                $entity = $db->find($id);
                $code = 404;
                $result = [];
                if($entity){
                    if(isset($data["zip_code"])){
                        $result = $db->upDate($id, ["zip_code" => $data["zip_code"]]);
                    }
                    else if(isset($data["city"])){
                        $result = $db->upDate($id, ["city" => $data["city"]]);
                    }
                    else if(isset($data["id_county"])){
                        $result = $db->upDate($id, ["id_county" => $data["id_county"]]);
                    }
                    if($result){
                        $code = 200;
                    }
                }

                Response::response($result, $code);
                break;
            default:
                echo "Bad Request";
                break;
        }
    }

    //szétdarabolja és ha az utolsó szám akkor az utolsó előttit adja vissza
    private static function getResourceName($uri)
    {
        $arrUri = explode("/", $uri);
        $last = $arrUri[count($arrUri) - 1]; //ha egy szam a vege akkor az egy id es elotte erroforras
        if (is_numeric($last)) {
            $last = $arrUri[count($arrUri) - 2];
        }
        return $last;
    }

    private static function getResourceId($uri)
    {
        // $arrUri = self::getArrUri($_SERVER('REQUEST.URI'));
        $arrUri = explode("/", $uri);
        $last = $arrUri[count($arrUri) - 1];
        if (is_numeric($last)) {
            return $last;
        }
        return false;
    }

    private static function getRequestData()
    {
        return json_decode(file_get_contents("php://input"), true);
    }


}