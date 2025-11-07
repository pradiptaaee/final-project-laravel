<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Author;
use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RatingController extends Controller
{
    public function create()
    {
        $authors = Author::orderBy('name')->get(['id', 'name']);
        return view('ratings.create', compact('authors'));
    }

    public function store(Request $request)
    {
        
        $request->validate([
            'book_id' => 'required|exists:books,id',
            'rating' => 'required|integer|min:1|max:10',
        ]);

        $userId = Auth::id(); 

        

        try {
            DB::beginTransaction();

            
            $book = Book::where('id', $request->book_id)
                ->where('author_id', $request->author_id)
                ->first();

            if (!$book) {
                return back()->withErrors(['book_id' => '❌ Buku tidak sesuai dengan penulis yang dipilih.'])->withInput();
            }

            //Cegah rating duplikat (user yang sama ke buku yang sama)
            $alreadyRated = Rating::where('user_id', $userId)
                ->where('book_id', $book->id)
                ->exists();

            if ($alreadyRated) {
                return back()->withErrors(['rating' => '❌ Anda sudah memberi rating untuk buku ini.'])->withInput();
            }

            //Batasi 1 rating per 24 jam untuk semua buku
            $lastRating = Rating::where('user_id', $userId)
                ->orderByDesc('created_at')
                ->first();

            if ($lastRating && $lastRating->created_at > now()->subDay()) {
                return back()->withErrors(['rating' => '⚠️ Anda hanya dapat memberi 1 rating setiap 24 jam.'])->withInput();
            }

            //Simpan rating 
            Rating::create([
                'user_id' => null,
                'book_id' => $book->id,
                'rating' => $request->rating,
            ]);

            DB::commit();

            return redirect()->route('books.index')->with('success', '✅ Rating berhasil disimpan!');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['system' => 'Terjadi kesalahan sistem: ' . $e->getMessage()]);
        }
    }
}
