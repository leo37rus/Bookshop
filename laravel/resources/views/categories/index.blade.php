<!DOCTYPE html>
<html>
<head>
    <title>Категории</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Категории</h1>
    @foreach ($categories as $category)
        <div class="mb-4">
            <div class="bg-gray-200 p-4 rounded-lg cursor-pointer" onclick="toggleBooks({{ $category->id }})">
                {{ $category->name }} (Книг: {{ $category->books->count() }}, Сумма: {{ $category->books->sum('price') }} руб.)
            </div>
            <div id="books-{{ $category->id }}" class="hidden pl-4 mt-2">
                @foreach ($category->books as $book)
                    <div class="bg-gray-100 p-2 rounded-lg mb-2">
                        @php($authors = $book->authors->pluck('name')->toArray())
                        {{ $book->name }} @if($authors) ({{ join(', ', $authors) }}) @endif - {{ $book->price }} руб. (Статус: {{ $book->status }})
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach
</div>

<script>
    function toggleBooks(categoryId) {
        const booksDiv = document.getElementById(`books-${categoryId}`);
        booksDiv.classList.toggle('hidden');
    }
</script>
</body>
</html>
