<?php
namespace App\Http\Controllers;
use App\Models\Author;
use App\Models\Rating;
use Illuminate\Http\Request;

class AuthorController extends Controller
{
    public function top(Request $request)
    {
        $now = now();
        $recentStart = $now->copy()->subDays(30);
        $previousStart = $now->copy()->subDays(60);
        $previousEnd = $recentStart->copy();

        // ambil authors + avg recent + avg previous + voter_count (rating>5)
        $authors = Author::select('authors.*')
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
            ->addSelect([
                'voter_count' => Rating::selectRaw('COUNT(*)')
                    ->join('books', 'ratings.book_id', 'books.id')
                    ->whereColumn('books.author_id', 'authors.id')
                    ->where('ratings.rating', '>', 5)
            ])
            ->orderByDesc('voter_count')
            ->limit(20)
            ->get()
            ->map(function ($a) {
                $recent = $a->avg_recent_month ?? 0;
                $prev = $a->avg_previous_month ?? 0;
                $weight = log(($a->voter_count ?: 0) + 1); // bobot log
                $a->trending_score = ($recent - $prev) * $weight;
                return $a;
            })
            ->sortByDesc('trending_score')
            ->take(20);

        // untuk each author ambil best/worst book (satu query per author — still ok untuk 20)
        foreach ($authors as $author) {
            $best = \App\Models\Book::where('author_id', $author->id)
                ->withAvg('ratings', 'rating')->orderByDesc('ratings_avg_rating')->first();
            $worst = \App\Models\Book::where('author_id', $author->id)
                ->withAvg('ratings', 'rating')->orderBy('ratings_avg_rating')->first();
            $author->best_book = $best;
            $author->worst_book = $worst;
        }

        return view('authors.top', compact('authors'));
    }
}
