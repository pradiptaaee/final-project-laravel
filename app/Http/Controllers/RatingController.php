<?php
namespace App\Http\Controllers;

use App\Models\Author;
use Illuminate\Http\Request;
use App\Models\Rating;
use Illuminate\Support\Facades\DB;

class RatingController extends Controller
{
    public function create()
    {
        // ambil list author (untuk dependent dropdown) dan sedikit buku contoh
        $authors = Author::limit(200)->get();
        return view('ratings.create', compact('authors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'book_id' => 'required|exists:books,id',
            'rating' => 'required|integer|min:1|max:10',
        ]);

        // SESSION RULE untuk project: 1 rating per 24 jam (apa pun bukunya)
        $lastRatedAt = session()->get('last_rating_time'); // ISO string
        if ($lastRatedAt) {
            $diffHours = now()->diffInHours(\Carbon\Carbon::parse($lastRatedAt));
            if ($diffHours < 24) {
                $hoursLeft = 24 - $diffHours;
                return back()->with('error', "Anda sudah memberi rating dalam 24 jam terakhir. Coba lagi dalam $hoursLeft jam.");
            }
        }

        // Concurrency safe: pakai transaction
        DB::beginTransaction();
        try {
            Rating::create([
                'book_id' => $request->book_id,
                'rating' => $request->rating,
                'user_id' => null,
            ]);
            DB::commit();

            // update session timestamp
            session()->put('last_rating_time', now()->toDateTimeString());

            return redirect()->route('books.index')->with('success', 'Terima kasih telah memberi rating!');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan rating. Coba lagi.');
        }
    }
}
