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

        $bookId = $request->book_id;

        // Ambil session ratings
        $ratedBooks = session()->get('rated_books', []);

        if (isset($ratedBooks[$bookId])) {
            $lastRated = $ratedBooks[$bookId];
            $diff = now()->diffInHours(Carbon::parse($lastRated));

            if ($diff < 24) {
                $hoursLeft = 24 - $diff;
                return back()->with('error', "Anda sudah memberi rating buku ini. Tunggu $hoursLeft jam lagi.");
            }
        }

        // Simpan rating ke database
        Rating::create([
            'book_id' => $bookId,
            'rating' => $request->rating,
            'user_id' => null, // tetap kosong, karena tidak ada user login
        ]);

        // Update session
        $ratedBooks[$bookId] = now()->toDateTimeString();
        session()->put('rated_books', $ratedBooks);

        return back()->with('success', 'Terima kasih telah memberi rating!');
    }
}
