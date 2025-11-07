<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Book;
use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // $query = Book::with(['author', 'category', 'ratings']);
        $query = Book::with(['author', 'category', 'ratings'])
            ->addSelect([
                'avg_rating' => Rating::selectRaw('AVG(rating)')
                    ->whereColumn('ratings.book_id', 'books.id'),

                'total_votes' => Rating::selectRaw('COUNT(*)')
                    ->whereColumn('ratings.book_id', 'books.id')
            ]);



        // --- FILTER BERDASARKAN KATEGORI DAN PENULIS (Tetap) ---
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('author_id')) {
            $query->where('author_id', $request->author_id);
        }
        if ($request->filled('publication_year')) {
            $query->where('publication_year', $request->publication_year);
        }

        // --- FITUR PENCARIAN RINGAN (Judul, ISBN, dan Penulis) ---
        if ($request->filled('search')) {
            $searchTerm = '%' . $request->search . '%';

           
            $authorIds = Author::where('name', 'like', $searchTerm)->pluck('id');

          
            $query->where(function ($q) use ($searchTerm, $authorIds) {
                $q->where('title', 'like', $searchTerm)     
                    ->orWhere('isbn', 'like', $searchTerm)
                    ->orWhereIn('author_id', $authorIds);
            });
        }

        // --- FILTER BERDASARKAN RATING RANGE BARU ---
        // $query = Book::with(['author', 'category'])
        //     ->withAvg('ratings', 'rating'); // menambahkan ratings_avg_rating

        // $minRating = $request->min_rating;
        // $maxRating = $request->max_rating;

        // if ($minRating || $maxRating) {
        //     if ($minRating) {
        //         $query->having('ratings_avg_rating', '>=', $minRating);
        //     }
        //     if ($maxRating) {
        //         $query->having('ratings_avg_rating', '<=', $maxRating);
        //     }
        // }


        // --- SORTING ---
        // $sortBy = $request->get('sort', 'weighted_avg'); // Default ke Weighted Average Rating

        // switch ($sortBy) {
        //     case 'total_votes':
        //         // Sorting berdasarkan kolom virtual 'total_votes' (jumlah votes)
        //         $query->orderBy('total_votes', 'desc');
        //         break;

        //     case 'alphabetical':
        //         // Sorting berdasarkan Judul
        //         $query->orderBy('title', 'asc');
        //         break;

        //     case 'recent_popularity':
        //         // Sorting berdasarkan jumlah votes dalam 30 hari terakhir (Membutuhkan Sub-query baru)
        //         $query->addSelect([
        //             'recent_votes' => Rating::selectRaw('COUNT(*)')
        //                 ->whereColumn('ratings.book_id', 'books.id')
        //                 ->where('created_at', '>=', now()->subDays(30))
        //         ])->orderBy('recent_votes', 'desc');
        //         break;

        //     // Default: Weighted Average Rating
        //     case 'weighted_avg':
        //     default:
        //         // Sorting berdasarkan kolom virtual 'avg_rating'
        //         // Default sorting: Rating tertinggi, buku dengan rating null/0 ditaruh di bawah.
        //         $query->orderBy('avg_rating', 'desc')->orderBy('total_votes', 'desc');
        //         break;
        // }

        
        $books = $query->paginate(20)->appends($request->query());

        return view('books.index', compact('books'));



    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
