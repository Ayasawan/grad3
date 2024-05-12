<?php

namespace App\Http\Controllers;
use App\Traits\ApiResponseTrait;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\ArticleResource;
use App\Models\Article;

class ArticleController extends Controller
{
    use  ApiResponseTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $article = ArticleResource::collection(Article::get());
        return $this->apiResponse($article, 'ok', 200);
    }

   
    public function store(Request $request)
    {
        $input=$request->all();
        $validator = Validator::make($input , [
            'name'=>'required',
            'description'=>'required',
            'image'=>['nullable',],
           
        ]);

        $file_name=$this->saveImage($request->image,'images/article');

        //   $file_name=$this->uploadImage($request->image);


        if ($validator->fails()){
            return $this->apiResponse(null,$validator ->errors() , 400);
        }
        // $fullImagePath = $file_name ? 'public/images/article/' . $file_name : null;

        $article = Article::query()->create([
            'name' => $request->name,
            'description' => $request->description,
            'image' => $file_name,

        ]);
        if($article) {
            return $this->apiResponse(new ArticleResource($article), 'This article save', 201);
        }
        return $this->apiResponse(null, 'This article not save', 400);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $article= Article::find($id);
        if($article){
            return $this->apiResponse(new ArticleResource($article) , 'ok' ,200);
        }
        return $this->apiResponse(null ,'the article not found' ,404);

    }

    
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,  $id)
    {
        $article= Article::find($id);
        if(!$article)
        {
            return $this->apiResponse(null ,'the article not found ',404);
        }
        $article->update($request->all());
        if($article)
        {
            return $this->apiResponse(new ArticleResource($article) , 'the article update',201);

        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        
        $article = Article::find($id);
        if(!$article)
        {
            return $this->apiResponse(null ,'the article not found ',404);
        }
        $article->delete($id);
        if($article)
            return $this->apiResponse(null ,'the  article delete ',200);
    }
}
