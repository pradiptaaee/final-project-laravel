<?php

namespace App\Http\Controllers;

use App\Models\Author;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuthorController extends Controller
{
    public function index(Request $request)
    {
        $query = Author::query()
            ->withCount('books') 
            ->select('authors.*', DB::raw('
                (SELECT ROUND(AVG(rating), 2)
                 FROM ratings
                 JOIN books ON books.id = ratings.book_id
                 WHERE books.author_id = authors.id
                ) AS avg_rating
            '), DB::raw('
                (SELECT COUNT(ratings.id)
                 FROM ratings
                 JOIN books ON books.id = ratings.book_id
                 WHERE books.author_id = authors.id AND ratings.rating > 5
                ) AS voters_count
            '));

        
        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

       
        $sort = $request->get('sort', 'name');

        switch ($sort) {
            case 'popular': 
                $query->orderByDesc('voters_count');
                break;
            case 'rating': 
                $query->orderByDesc('avg_rating');
                break;
            default:
                $query->orderBy('name');
                break;
        }

        
        $authors = $query->paginate(10)->appends($request->query());

        return view('authors.index', compact('authors', 'sort'));

        

        
    }

    public function create()
    {
        return view('authors.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            
        ]);

        Author::create($request->all());

        return redirect()->route('authors.index')->with('success', 'Author berhasil ditambahkan.');
    }

    public function edit(Author $author)
    {
        return view('authors.edit', compact('author'));
    }

    public function update(Request $request, Author $author)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            
        ]);

        $author->update($request->all());

        return redirect()->route('authors.index')->with('success', 'Author berhasil diperbarui.');
    }

    public function destroy(Author $author)
    {
        $author->delete();
        return redirect()->route('authors.index')->with('success', 'Author berhasil dihapus.');
    }
}
