<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookstore - @yield('title')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand fw-bold" href="{{ route('books.index') }}">📚 Bookstore</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link" href="{{ route('books.index') }}">Books</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('authors.index') }}">Authors</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('ratings.create') }}">Rate a Book</a></li>
        
      </ul>

    </div>
  </div>
</nav>

<div class="container py-4">
  @yield('content')
</div>

</body>
</html>
