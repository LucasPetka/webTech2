<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Classh;
use App\Http\Resources\Classh as ClasshResource;
use DB;

class ClasshController extends Controller
{

    //===================================GET ALL=================================================
    public function getAll(Request $request){
        
        $classes = Classh::all();

        return ClasshResource::collection($classes);

    }


    //======================================STORE CLASS==========================================
    public function store(Request $request)
    {
        $name=false;
        $cid=false;
        $pid=false;
        $security = true;
        $error_arr = [];
        
        if($request->isMethod('put')){
            $class = Classh::findOrFail($request->cid);
            $security = false;
        }   
        else{
            $class = new Classh;
        }


        //$class->cid = $request->input('cid');
        $class->name = $request->input('name');
        $class->pid = $request->input('pid');
        $class->abstract = $request->input('abstract');

        if($security){
            if (Classh::where('name', '=', $class->name)->exists()) {
                $name=true;
                array_push($error_arr,"class name '". $class->name ."' already exists.");
            }
            
            /*if (Classh::where('cid', '=', $class->cid)->exists()) {
                $cid=true;
                array_push($error_arr,"cid '". $class->cid ."' already exists.");
            }*/

            if($class->pid != null){
                if (!Classh::where('cid', '=', $class->pid)->exists()) {
                    $pid=true;
                    array_push($error_arr,"pid '". $class->pid ."' not found.");
                }
            }else{
                $class->pid = 0;
            }

            if($name==false && $cid==false && $pid==false){
                $class->save();
                return response()->json(["ret" => "true"]);
            }else{
                return response()->json([
                    "ret"=> "false",
                    "message"=> $error_arr,
                ]);
            }


        }else{

            if($class->save()){
                return response()->json(["ret" => "true"]);
            }else{
                return response()->json([
                    "ret"=> "false",
                    "message"=> "Error",
                ]);
            }

        }
    }

    
    //======================================SHOW CLASS==========================================
    public function show($id)
    {
        $class = Classh::find($id);
        if($class){
            return response()->json([
                "cid" => $class->cid,
                "name" => $class->name,
                "abstract" => $class->abstract
            ]);
        }
        else{
            return response()->json([
                "ret"=> "false",
                "message"=> "cid ". $id ." does not exist"
            ]);
        }

    }

    //=======================================UPDATE CLASS========================================

    public function update(Request $request)
    {
        $class = Classh::find($id);
        if($class){
            return response()->json([
                "cid" => $class->cid,
                "name" => $class->name,
                "abstract" => $class->abstract
            ]);
        }
        else{
            return response()->json([
                "ret"=> "false",
                "message"=> "cid ". $id ." does not exist"
            ]);
        }

    }




    //======================================DELETE CLASS==========================================
    public function destroy($id)
    {
        $class = Classh::findOrFail($id);
        $classes = Classh::all()->toArray();

        if($class->delete()){

            $this->deleteSub($classes,null,$class->cid);
            return response()->json(["ret" => "true"]);
        }
        else{
            return response()->json([
                "ret"=> "false",
                "message"=> "cid ". $id ." does not exist"
            ]);
        }
    }


    public function deleteSub(&$array,$baseRoot,$id){
        foreach($array as &$element)
        {
            $cpy=null;
            if(
                ($baseRoot==null&&$element['pid']== $id)||
                ($baseRoot!==null&&$element['pid']==$baseRoot['cid'])
            ){
                $cpy=$element;
                $class = Classh::findOrFail($cpy["cid"]);
                $class->delete();
                $this->deleteSub($array,$cpy, $cpy["cid"]);
                
            }
        }
    }

    
    //======================================SUPER CLASSES==========================================
    public function superclasses($id)
    {
        $classes = Classh::all(); // get all classes
        $cl = Classh::find($id); // get class by id

        if($cl){
            $pid = $cl->pid; // get parrent id
            $collection = collect(); //create collection
            $collection->push($cl); //push first class

            while($pid != 0){
                if($cl->pid != 0){
                    foreach ($classes as $class) {
                        if($class->cid == $cl->pid){
                            $cl = $class;
                            $collection->push($cl); //push every parent class until parent ID equals 0
                        }
                    }
                }else{
                    break;
                }
            };

            return response()->json(["list" => $collection->toArray() ]);
        }
        else{
            return response()->json([
                "ret"=> "false",
                "message"=> "cid ". $id ." does not exist"
            ]);
        }
    }

    //======================================SUB CLASSES==========================================
    public function subclasses($id)
    {
        $classes = Classh::all()->toArray(); // get all classes
        $class = Classh::find($id);
        $subArray = [];

        if($class){
            $subclasses = str_replace(",\"children\":[]","",$this->foo($classes, $class));
            array_push($subArray,$subclasses);
            return $subArray;
        }
        else{
            return response()->json([
                "ret"=> "false",
                "message"=> "cid ". $id ." does not exist"
            ]);
        }

    }


    //======================================PRINTING SUB CLASSES==========================================

    public function findRoots(&$array,$baseRoot,$id){
        $roots=[];
        foreach($array as &$element)
        {
            $cpy=null;
            if(
                ($baseRoot==null&&$element['pid']== $id)||
                ($baseRoot!==null&&$element['pid']==$baseRoot['cid'])
            ){
                $cpy=$element;
                array_push($roots,[
                    'id'=>$cpy["cid"],
                    'name'=>$cpy["name"],
                    'children'=> $this->findRoots($array,$cpy, $id)
                    ]);
            }
        }
        return $roots;
    }

    public function foo($array,$class){
        $object=[
            "id"=>$class->cid,
            "name"=>$class->name,
            "children"=>[]
        ];
        $roots= $this->findRoots($array,null,$class->cid);
        $object["children"]=$roots;
        if(empty($object["children"])){

            return json_encode([
                "ret"=> "false",
                "message"=> "cid ". $class->cid ." has no subclasses"
            ]);

        }else{
            return $object;
        }

    }







}
