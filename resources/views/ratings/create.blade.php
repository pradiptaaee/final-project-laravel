@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">⭐ Give a Rating</h2>

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('ratings.store') }}" class="card p-4 shadow-sm">
        @csrf
        <div class="mb-3">
            <label class="form-label">Author</label>
            <select name="author_id" id="author_id" class="form-select" required>
                <option value="">-- Select Author --</option>
                @foreach($authors as $author)
                    <option value="{{ $author->id }}">{{ $author->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Book</label>
            <select name="book_id" id="book_id" class="form-select" required>
                <option value="">-- Select Book --</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Rating (1–10)</label>
            <input type="number" name="rating" class="form-control" min="1" max="10" required>
        </div>

        <button class="btn btn-primary w-100">Submit</button>
    </form>
</div>

<script>
document.getElementById('author_id').addEventListener('change', async function() {
    const authorId = this.value;
    const bookSelect = document.getElementById('book_id');
    bookSelect.innerHTML = '<option>Loading...</option>';

    if (!authorId) {
        bookSelect.innerHTML = '<option>-- Select Book --</option>';
        return;
    }

    const response = await fetch(`/api/books/by-author/${authorId}`);
    const books = await response.json();

    bookSelect.innerHTML = '<option value="">-- Select Book --</option>';
    books.forEach(book => {
        bookSelect.innerHTML += `<option value="${book.id}">${book.title}</option>`;
    });
});
</script>
@endsection
