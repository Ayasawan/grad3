<?php


namespace App\Traits;

use Illuminate\Http\Request;
trait ApiResponseTrait
{
    public function  apiResponse($data= null ,$message=null ,$status= null)
    {
        $array=[
            'data'=> $data,
            'message' =>$message,
            'status' => $status,
        ];
        return response($array,$status);
    }
    // public function saveImage($photo,$folder){
    //     $file_extension =$photo->getClientOriginalExtension();
    //     $file_name =time().'.'.$file_extension;
    //     $path = $folder;
    //     $photo->move($path,$file_name);
    //     return $file_name;

    // }



    public function saveImage($photo,$folder){
        $file_extension =$photo->getClientOriginalExtension();
        $file_name =time().'.'.$file_extension;
        $path = $folder;
        $photo->move($path,$file_name);
        $fullImagePath = $file_name ? 'images/article/' . $file_name : null;
        $host = $_SERVER['HTTP_HOST'];
        $fullPath = 'http://' . $host . '/' . $fullImagePath;
        return $fullPath;

    }

}
