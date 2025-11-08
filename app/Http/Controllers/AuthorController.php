<?php
namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Rating;
use App\Models\Book;
use Illuminate\Http\Request;

class AuthorController extends Controller
{
    public function top(Request $request)
    {
        $filter = $request->get('filter', 'trending');

        $now = now();
        $recentStart = $now->copy()->subDays(30);
        $previousStart = $now->copy()->subDays(60);
        $previousEnd = $recentStart->copy();

        // Base query untuk semua author
        $authors = Author::select('authors.*')
            ->addSelect([
                'voter_count' => Rating::selectRaw('COUNT(*)')
                    ->join('books', 'ratings.book_id', 'books.id')
                    ->whereColumn('books.author_id', 'authors.id')
            ])
            ->addSelect([
                'avg_rating_all' => Rating::selectRaw('AVG(rating)')
                    ->join('books', 'ratings.book_id', 'books.id')
                    ->whereColumn('books.author_id', 'authors.id')
            ])
            ->addSelect([
                'avg_recent_month' => Rating::selectRaw('AVG(rating)')
                    ->join('books', 'ratings.book_id', 'books.id')
                    ->whereColumn('books.author_id', 'authors.id')
                    ->where('ratings.created_at', '>=', $recentStart)
            ])
            ->addSelect([
                'avg_previous_month' => Rating::selectRaw('AVG(rating)')
                    ->join('books', 'ratings.book_id', 'books.id')
                    ->whereColumn('books.author_id', 'authors.id')
                    ->whereBetween('ratings.created_at', [$previousStart, $previousEnd])
            ])
            ->get();

        // Hitung trending score
        foreach ($authors as $a) {
            $recent = $a->avg_recent_month ?? 0;
            $prev = $a->avg_previous_month ?? 0;
            $weight = log(($a->voter_count ?: 0) + 1);

            $a->trending_score = ($recent - $prev) * $weight;
        }

        // FILTER LOGIC
        switch ($filter) {
            case 'popularity':
                $authors = $authors->sortByDesc('voter_count')->take(20);
                break;

            case 'average':
                $authors = $authors->sortByDesc('avg_rating_all')->take(20);
                break;

            case 'trending':
            default:
                $authors = $authors->sortByDesc('trending_score')->take(20);
                break;
        }

        // Tambah best/worst book
        foreach ($authors as $author) {
            $author->best_book = Book::where('author_id', $author->id)
                ->withAvg('ratings', 'rating')
                ->orderByDesc('ratings_avg_rating')
                ->first();

            $author->worst_book = Book::where('author_id', $author->id)
                ->withAvg('ratings', 'rating')
                ->orderBy('ratings_avg_rating')
                ->first();
        }

        return view('authors.top', compact('authors'));
    }
}
