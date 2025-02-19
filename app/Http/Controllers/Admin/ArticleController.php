<?php

namespace App\Http\Controllers\Admin;

use App\Models\Article;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ArticleController extends Controller
{
    public function index()
    {
        $articles = Article::latest()->paginate(20);
        return view('admin.article', compact('articles'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title'   => 'required|string|max:255',
            'content' => 'required',
            'image'   => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'created_at' => 'nullable',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput()->with('error', 'Invalid input data');
        }

        $imagePath = $request->file('image') ? $request->file('image')->store('blogs', 'public') : null;
        
        Article::create([
            'title'   => $request->title,
            'slug'    => Str::slug($request->title),
            'content' => $request->content,
            'image'   => $imagePath,
            'created_at'  => $request->created_at ? Carbon::parse($request->created_at)->format('Y-m-d H:i:s') : now(),
        ]);

        return redirect()->back()->with('success', 'Article created successfully.');
    }

    public function update(Request $request, Article $article)
    {
        $validator = Validator::make($request->all(), [
            'title'   => 'required|string|max:255',
            'content' => 'required',
            'image'   => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'created_at' => 'nullable',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput()->with('error', 'Invalid input data');
        }

        $imagePath = $article->image;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('blogs', 'public');
        }

        $article->update([
            'title'   => $request->title,
            'slug'    => Str::slug($request->title),
            'content' => $request->content,
            'image'   => $imagePath,
            'created_at'  => $request->created_at ? Carbon::parse($request->created_at)->format('Y-m-d H:i:s') : now(),
        ]);

        return redirect()->back()->with('success', 'Article updated successfully.');
    }

    public function toggle(Request $request, Article $article)
    {
        $validator = Validator::make($request->all(), [
            'action' => ['required', 'in:enabled,disabled'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput()->with('error', 'Invalid input data');
        }

        $article->update([
            'status' => $request->action,
        ]);

        if($article)
            return back()->with('success', 'Article updated successfully.');

        return back()->withInput()->with('error', 'Error processing data');
    }

    public function destroy(Article $article)
    {
        $article->delete();
        return redirect()->back()->with('success', 'Article deleted successfully.');
    }
}
