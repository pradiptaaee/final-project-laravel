@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Give a Rating</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('ratings.store') }}" method="POST" class="card p-4 shadow-sm">
        @csrf

        {{-- Author --}}
        <div class="mb-3">
            <label class="form-label">Author</label>
            <select name="author_id" id="author_id" class="form-select" required>
                <option value="">-- Select Author --</option>
                @foreach($authors as $author)
                    <option value="{{ $author->id }}">{{ $author->name }}</option>
                @endforeach
            </select>
        </div>

        {{-- Book --}}
        <div class="mb-3">
            <label class="form-label">Book</label>
            <select name="book_id" id="book_id" class="form-select" required>
                <option value="">-- Select Book --</option>
            </select>
        </div>

        {{-- Rating --}}
        <div class="mb-3">
            <label class="form-label">Rating (1-10)</label>
            <select name="rating" class="form-select" required>
                @for($i = 1; $i <= 10; $i++)
                    <option value="{{ $i }}">{{ $i }}</option>
                @endfor
            </select>
        </div>

        <button class="btn btn-success w-100">Submit Rating</button>
    </form>
</div>

<script>
document.getElementById('author_id').addEventListener('change', async function() {
    const authorId = this.value;
    const bookSelect = document.getElementById('book_id');
    bookSelect.innerHTML = '<option value="">Loading...</option>';

    if (!authorId) {
        bookSelect.innerHTML = '<option value="">-- Select Book --</option>';
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
