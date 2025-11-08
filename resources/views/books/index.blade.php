@extends('layouts.app')

@section('content')
    <div class="container mt-4">
        <h2 class="mb-4">📚 Book List</h2>
        <div class="card mb-3 p-2 pt-4">
            {{-- Filter dan Search --}}
            <form method="GET" action="{{ route('books.index') }}" class="row g-2 mb-3">
                <div class="col-md-12 gap-2 d-flex mb-4">
                    <div class="col-6">
                        <input type="text" name="search" class="form-control" placeholder="Search title, author, ISBN..."
                            value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary w-100">Cari</button>
                    </div>

                    <div class="input-group">
                        <label class="input-group-text" for="sort">Sort by</label>
                        <select class="form-select" name="sort" id="sort" onchange="this.form.submit()">
                            <option value="weighted_avg" {{ request('sort') == 'weighted_avg' ? 'selected' : '' }}>Weighted
                                Average (default)</option>
                            <option value="total_votes" {{ request('sort') == 'total_votes' ? 'selected' : '' }}>Total Votes
                            </option>
                            <option value="recent_popularity"
                                {{ request('sort') == 'recent_popularity' ? 'selected' : '' }}>Recent Popularity</option>
                            <option value="alphabetical" {{ request('sort') == 'alphabetical' ? 'selected' : '' }}>
                                Alphabetical (A–Z)</option>
                        </select>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <select name="author_id" class="form-select">
                        <option value="">All Authors</option>
                        @foreach ($authors as $author)
                            <option value="{{ $author->id }}" {{ request('author_id') == $author->id ? 'selected' : '' }}>
                                {{ $author->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="dropdown col-md-3">

                    <!-- BUTTON DROPDOWN -->
                    <button class="form-control dropdown-toggle d-flex justify-content-between align-items-center"
                        type="button" id="categoryDropdownButton" data-bs-toggle="dropdown" aria-expanded="false">
                        <span id="categorySelectedText">
                            @if (request('categories'))
                                {{ \App\Models\Category::whereIn('id', request('categories'))->pluck('name')->join(', ') }}
                            @else
                                Filter by Category
                            @endif
                        </span>
                    </button>

                    <!-- DROPDOWN LIST -->
                    <ul class="dropdown-menu w-100 p-2" aria-labelledby="categoryDropdownButton"
                        style="max-height: 250px; overflow-y:auto;">

                        @foreach ($categories as $cat)
                            <li class="dropdown-item">
                                <label class="d-flex align-items-center">
                                    <input type="checkbox" name="categories[]" value="{{ $cat->id }}"
                                        class="form-check-input me-2 category-checkbox"
                                        {{ in_array($cat->id, request('categories', [])) ? 'checked' : '' }}>
                                    {{ $cat->name }}
                                </label>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="col-md-2">
                    <select name="publication_year" id="publication_year" class="form-select">
                        <option value="">All Years</option>
                        @foreach ($publication_years as $year)
                            <option value="{{ $year }}"
                                {{ request('publication_year') == $year ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <input type="number" name="min_rating" class="form-control" placeholder="Min rating"
                        value="{{ request('min_rating') }}">
                </div>
                <div class="col-md-2">
                    <input type="number" name="max_rating" class="form-control" placeholder="Max rating"
                        value="{{ request('max_rating') }}">
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary w-100">Filter</button>
                </div>
            </form>
        </div>

        {{-- Tabel Buku --}}
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Category</th>
                        <th>ISBN</th>
                        <th>Publication Year</th>
                        <th>Avg Rating</th>
                        <th>Total Votes</th>
                        <th>Rate</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($books as $book)
                        <tr>
                            <td>{{ $book->title }}</td>
                            <td>{{ $book->author->name ?? '-' }}</td>
                            <td>{{ $book->category->name ?? '-' }}</td>
                            <td>{{ $book->isbn }}</td>
                            <td>{{ $book->publication_year }}</td>
                            <td class="text-center">
                                <span class="badge bg-success fs-6">
                                    {{ number_format($book->ratings_avg_rating ?? 0, 2) }}
                                </span>
                            </td>
                            <td class="text-center">{{ $book->ratings_count }}</td>
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
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="d-flex justify-content-center">
            {{ $books->links() }}
        </div>
    </div>



    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const checkboxes = document.querySelectorAll('.category-checkbox');
            const textBox = document.getElementById('categorySelectedText');

            function updateText() {
                let selected = [];
                checkboxes.forEach(cb => {
                    if (cb.checked) selected.push(cb.parentElement.textContent.trim());
                });

                textBox.textContent = selected.length ?
                    selected.join(', ') :
                    'Filter by Category';
            }

            // Update saat user mengklik kategori
            checkboxes.forEach(cb => cb.addEventListener('change', updateText));

            // Update setelah filter dijalankan
            updateText();
        });
    </script>
@endsection
