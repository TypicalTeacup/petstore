@php
    $categories = \App\Models\Category::all();
    $tags = \App\Models\Tag::all();
@endphp

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    @vite(['resources/css/app.css'])
    <title>Nowe zwierzę</title>
</head>
<body class="m-4 mx-auto container">
    <h1 class="text-center m-4 font-bold text-4xl">Nowe zwierzę</h1>
    <div class="mx-auto flex flex-col gap-4">
        <label for="name">
            Imię
            <input class="border" type="text" name="name" id="name" />
        </label>
        <label for="category">
            Kategoria
            <select name="category" id="category">
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </label>
        <label for="status">
            Status
            <select class="border" name="status" id="status">
                <option value="available">Dostępny</option>
                <option value="pending">W toku</option>
                <option value="sold">Sprzedany</option>
            </select>
        </label>
        <label for="tags">
            Status
            <select multiple class="border" name="tags" id="tags">
                @foreach($tags as $tag)
                    <option value="{{ $tag->id }}">{{ $tag->name }}</option>
                @endforeach
            </select>
        </label>
    </div>
    <button class="border bg-gray-400 p-1 rounded" onclick="submit()">Dodaj</button>
</body>
<script>
    const name = document.getElementById('name');
    const category = document.getElementById('category');
    const status = document.getElementById('status');
    const tags = document.getElementById('tags');

    function submit() {
        fetch('/api/pet', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                name: name.value,
                category: {
                    id: category.value
                },
                status: status.value,
                tags: Array.from(tags.selectedOptions).map(option =>({
                        id: option.value
                }))
            })
        })
            .then(response => response.json())
            .then(data => {
                window.location.href = '/pet';
            });
    }
</script>
</html>
