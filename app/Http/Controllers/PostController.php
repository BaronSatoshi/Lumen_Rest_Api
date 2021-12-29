<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    //Retorna todos os resultados
    public function index(){
        return Post::all();

    }

    //Cria um novo dado
    public function store(Request $request){
        try{
            $post = new Post();
            $post->title = $request->title;
            $post->body = $request->body;

            if($post->save()){
                return response()->json(['status' => 'sucess','message' => 'Post created sucessfully']);
            }
        }catch(Exception $e){
            return response()->json(['status' => 'error','message' => $e->getMessage()]);
        }
    }

    //Edita o conteudo escolhido
    public function update(Request $request, $id){
        try{
            $post = Post::findOrFail($id);
            $post->title = $request->title;
            $post->body = $request->body;

            if($post->save()){
                return response()->json(['status' => 'sucess','message' => 'Post update sucessfully']);
            }
        }catch(Exception $e){
            return response()->json(['status' => 'error','message' => $e->getMessage()]);
        }
    }

    //Exclui um dado
    public function destroy($id){
        try{
            $post = Post::findOrFail($id);
            
            if($post->delete()){
                return response()->json(['status' => 'sucess','message' => 'Post deleted sucessfully']);
            }
        }catch(Exception $e){
            return response()->json(['status' => 'error','message' => $e->getMessage()]);
        }
    }
}

