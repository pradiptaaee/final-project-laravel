@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-3">Author Lists</h2>

    <form method="GET" class="row g-2 mb-4">
        <div class="col-md-4">
            <input type="text" name="name" class="form-control" placeholder="Search author..." value="{{ request('name') }}">
        </div>
        <div class="col-md-3">
            <select name="sort" class="form-select" onchange="this.form.submit()">
                <option value="name" {{ $sort == 'name' ? 'selected' : '' }}>Sort by Name</option>
                <option value="popular" {{ $sort == 'popular' ? 'selected' : '' }}>Sort by Popularity</option>
                <option value="rating" {{ $sort == 'rating' ? 'selected' : '' }}>Sort by Rating</option>
            </select>
        </div>
        <div class="col-md-2">
            <button class="btn btn-primary w-100">Filter</button>
        </div>
    </form>

    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>Author</th>
                <th>Total Books</th>
                <th>Average Rating</th>
                <th>Voters (>5)</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($authors as $author)
                <tr>
                    <td>{{ $author->name }}</td>
                    <td>{{ $author->books_count }}</td>
                    <td>{{ $author->avg_rating ?? '-' }}</td>
                    <td>{{ $author->voters_count ?? 0 }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-center">No authors found.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="d-flex justify-content-center mt-3">
        {{ $authors->links() }}
    </div>
</div>
@endsection
