@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2 class="mb-3">🏆 Top 20 Authors</h2>

    {{-- Tab Filter --}}
    <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
            <a href="?filter=popularity" class="nav-link {{ request('filter') == 'popularity' ? 'active' : '' }}">By Popularity</a>
        </li>
        <li class="nav-item">
            <a href="?filter=average" class="nav-link {{ request('filter') == 'average' ? 'active' : '' }}">By Average Rating</a>
        </li>
        <li class="nav-item">
            <a href="?filter=trending" class="nav-link {{ request('filter') == 'trending' ? 'active' : '' }}">Trending</a>
        </li>
    </ul>

    {{-- Daftar Penulis --}}
    <table class="table table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Author</th>
                <th>Total Ratings</th>
                <th>Best Book</th>
                <th>Worst Book</th>
                <th>Trending Score</th>
            </tr>
        </thead>
        <tbody>
            @foreach($authors as $index => $author)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $author->name }}</td>
                <td>{{ $author->voter_count }}</td>
                <td>{{ $author->best_book->title ?? '-' }}</td>
                <td>{{ $author->worst_book->title ?? '-' }}</td>
                <td>
                    {{ number_format($author->trending_score ?? 0, 2) }}
                    @if(($author->trending_score ?? 0) > 0)
                        🔥
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
