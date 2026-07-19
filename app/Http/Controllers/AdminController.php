<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class AdminController extends Controller
{
    // --- Users ---
    public function getUsers(): JsonResponse
    {
        $users = DB::table('users')->select('id', 'name', 'email', 'created_at')->get();
        return response()->json($users);
    }
    
    public function deleteUser($id): JsonResponse
    {
        DB::table('users')->where('id', $id)->delete();
        return response()->json(['success' => true]);
    }

    // --- Ports ---
    public function getPorts(): JsonResponse
    {
        $ports = DB::table('ports')->orderBy('id', 'desc')->take(100)->get();
        return response()->json($ports);
    }

    public function savePort(Request $request): JsonResponse
    {
        $data = $request->only(['name', 'country', 'latitude', 'longitude', 'code']);
        
        if ($request->has('id') && $request->id) {
            DB::table('ports')->where('id', $request->id)->update($data);
        } else {
            DB::table('ports')->insert($data);
        }
        
        return response()->json(['success' => true]);
    }

    public function deletePort($id): JsonResponse
    {
        DB::table('ports')->where('id', $id)->delete();
        return response()->json(['success' => true]);
    }

    // --- Articles ---
    public function getArticles(): JsonResponse
    {
        $articles = DB::table('articles')->orderBy('id', 'desc')->get();
        return response()->json($articles);
    }

    public function saveArticle(Request $request): JsonResponse
    {
        $data = $request->only(['title', 'content', 'author']);
        
        if ($request->has('id') && $request->id) {
            DB::table('articles')->where('id', $request->id)->update($data + ['updated_at' => now()]);
        } else {
            DB::table('articles')->insert($data + ['created_at' => now(), 'updated_at' => now()]);
        }
        
        return response()->json(['success' => true]);
    }

    public function deleteArticle($id): JsonResponse
    {
        DB::table('articles')->where('id', $id)->delete();
        return response()->json(['success' => true]);
    }
}
