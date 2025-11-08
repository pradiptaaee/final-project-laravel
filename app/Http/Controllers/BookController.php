<?php
namespace App\Http\Controllers;
use App\Models\Book;
use App\Models\Author;
use Illuminate\Http\Request;
use App\Models\Category;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::select('id', 'name')->orderBy('name')->get();
        $authors = Author::select('id', 'name')->orderBy('name')->get();

        $query = Book::with(['author', 'category'])
            ->withAvg('ratings', 'rating')
            ->withCount('ratings');

        // FILTER CATEGORY (single select)
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // FILTER AUTHOR
        if ($request->filled('author_id')) {
            $query->where('author_id', $request->author_id);
        }

        // FILTER LAIN
        if ($request->filled('publication_year')) {
            $query->where('publication_year', $request->publication_year);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('location')) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }

        // RATING RANGE
        if ($request->filled('min_rating') || $request->filled('max_rating')) {
            $min = $request->min_rating;
            $max = $request->max_rating;
            if ($min)
                $query->having('ratings_avg_rating', '>=', (float) $min);
            if ($max)
                $query->having('ratings_avg_rating', '<=', (float) $max);
        }

        // PENCARIAN
        if ($request->filled('search')) {
            $s = '%' . $request->search . '%';
            $authorIds = Author::where('name', 'like', $s)->limit(200)->pluck('id')->toArray();
            $query->where(function ($q) use ($s, $authorIds) {
                $q->where('title', 'like', $s)
                    ->orWhere('isbn', 'like', $s)
                    ->orWhereIn('author_id', $authorIds);
            });
        }

        // SORTING
        $sort = $request->get('sort', 'weighted_avg');
        switch ($sort) {
            case 'total_votes':
                $query->orderByDesc('ratings_count');
                break;
            case 'alphabetical':
                $query->orderBy('title', 'asc');
                break;
            default:
                $query->orderByDesc('ratings_avg_rating')->orderByDesc('ratings_count');
        }

        $books = $query->paginate(20)->appends($request->query());
        $publication_years = Book::select('publication_year')->distinct()->pluck('publication_year');


        return view('books.index', compact('books', 'authors', 'categories', 'publication_years'));
    }
}
