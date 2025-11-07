@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <h2 class="mb-4 fw-bold">📚 Book List</h2>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                
                <form method="GET" class="row g-3">

                   
                    <div class="col-md-8">
                        <label for="search" class="form-label fw-semibold">Cari Buku </label>
                        <input type="text" class="form-control" id="search" name="search"
                            placeholder="(Judul, Penulis, ISBN, Penerbit)" value="{{ request('search') }}">
                    </div>

                    
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search me-1"></i> Cari
                        </button>
                    </div>

                    <div class="col-md-2 d-flex align-items-end">
                        <a href="{{ route('books.index') }}" class="btn btn-outline-secondary w-100">Reset Filter</a>
                    </div>

                    
                    <div class="col-md-4">
                        <label for="sort" class="form-label fw-semibold">Urutkan Berdasarkan</label>
                        <select name="sort" id="sort" class="form-select">
                            
                            <option value="weighted_avg"
                                {{ request('sort', 'weighted_avg') == 'weighted_avg' ? 'selected' : '' }}>
                                Weighted Average Rating (Default)
                            </option>
                            <option value="total_votes" {{ request('sort') == 'total_votes' ? 'selected' : '' }}>
                                Total Votes
                            </option>
                            <option value="recent_popularity"
                                {{ request('sort') == 'recent_popularity' ? 'selected' : '' }}>
                                Recent Popularity (30 Hari)
                            </option>
                            <option value="alphabetical" {{ request('sort') == 'alphabetical' ? 'selected' : '' }}>
                                Alphabetical (A–Z)
                            </option>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Category</label>
                        <select name="category_id" class="form-select">
                            <option value="">All Categories</option>
                            @foreach (\App\Models\Category::all() as $category)
                                <option value="{{ $category->id }}"
                                    {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Author</label>
                        <select name="author_id" class="form-select">
                            <option value="">All Authors</option>
                            @foreach (\App\Models\Author::all() as $author)
                                <option value="{{ $author->id }}"
                                    {{ request('author_id') == $author->id ? 'selected' : '' }}>
                                    {{ $author->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Year</label>
                        <input type="number" name="publication_year" class="form-control"
                            value="{{ request('publication_year') }}">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Rating Range</label>
                        <div class="d-flex gap-2">
                            <input type="number" name="min_rating" min="1" max="10" class="form-control"
                                placeholder="Min Rate" value="{{ request('min_rating') }}">
                            <input type="number" name="max_rating" min="1" max="10" class="form-control"
                                placeholder="Max Rate" value="{{ request('max_rating') }}">
                        </div>
                    </div>

                    
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search me-1"></i> Filter
                        </button>
                    </div>

                </form>
            </div>
        </div>

        {{-- Book List --}}
        <div class="table-responsive shadow-sm">
            <table class="table table-striped table-bordered align-middle">
                <thead class="table-dark text-center">
                    <tr>
                        <th>Title</th>
                        <th>ISBN</th>
                        <th>Author</th>
                        <th>Category</th>
                        <th>Publication Year</th>
                        <th>Average Rating</th>
                        <th>Voters</th>
                        <th>Status</th>
                        <th>Rate</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($books as $book)
                        <tr>
                            <td class="fw-semibold">{{ $book->title }}</td>
                            <td>{{ $book->isbn }}</td>
                            <td>{{ $book->author->name }}</td>
                            <td>{{ $book->category->name }}</td>
                            <td>{{ $book->publication_year }}</td>
                            <td class="text-center">
                                <span class="badge bg-success fs-6">
                                    {{ number_format($book->ratings_avg_rating ?? 0, 2) }}
                                </span>
                            </td>
                            <td class="text-center">{{ $book->total_votes ?? $book->ratings->count() }}</td>
                            {{-- <td class="text-center">{{ $book->ratings->count() }}</td> --}}
                            <td>
                                @if ($book->status == 'available')
                                    <span class="badge bg-success">Available</span>
                                @elseif($book->status == 'rented')
                                    <span class="badge bg-warning text-dark">Rented</span>
                                @else
                                    <span class="badge bg-secondary">Reserved</span>
                                @endif
                            </td>
                            <td>
                                <form action="{{ route('ratings.store') }}" method="POST" class="d-flex gap-2">
                                    @csrf
                                    <input type="hidden" name="book_id" value="{{ $book->id }}">
                                    <input type="number" name="rating" min="1" max="10"
                                        class="form-control form-control-sm w-50" placeholder="1-10" required>
                                    <button type="submit" class="btn btn-sm btn-primary">
                                        <i class="bi bi-star-fill"></i> Rate
                                    </button>
                                </form>

                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">No books found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="d-flex justify-content-center mt-4">
            {{-- Pastikan Anda menggunakan 'pagination::bootstrap-5' atau sesuai dengan template pagination Anda --}}
            {{ $books->links('pagination::bootstrap-5') }}
        </div>
    </div>
@endsection
