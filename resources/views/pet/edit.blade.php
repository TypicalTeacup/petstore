@php
    $categories = \App\Models\Category::all();
    $tags = \App\Models\Tag::all();
@endphp

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite(['resources/css/app.css'])
    <title>Edytuj</title>
</head>
<body class="m-4 mx-auto container">
    <h1 class="text-center m-4 font-bold text-4xl">Edytuj</h1>
    <div class="flex flex-col gap-4 mx-auto mb-4">
        <label for="name">
            Imię
            <input class="border" type="text" name="name" id="name" value="{{ $pet->name }}" />
        </label>
        <label for="category">
            Kategoria
            <select name="category" id="category">
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ $pet->category_id === $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                @endforeach
            </select>
        </label>
        <label for="status">
            Status
            <select class="border" name="status" id="status">
                <option value="available" {{ $pet->status === 'available' ? 'selected' : '' }}>Dostępny</option>
                <option value="pending" {{ $pet->status === 'pending' ? 'selected' : '' }}>W toku</option>
                <option value="sold" {{ $pet->status === 'sold' ? 'selected' : '' }}>Sprzedany</option>
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
    <button class="border bg-gray-400 p-1 rounded" onclick="submit()">Zapisz</button>
</body>

<script>
    const status = document.getElementById('status');
    const name = document.getElementById('name');
    const tags = document.getElementById('tags');
    const category = document.getElementById('category');

    function submit() {
        fetch('/api/pet', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                id: {{ $pet->id }},
                name: name.value,
                category: {
                    id: category.value
                },
                status: status.value,
                tags: Array.from(tags.selectedOptions).map(option => ({id: option.value})),
            })
        }).then(() => {
            window.location.href = '/pet/{{ $pet->id }}';
        });
    }
</script>
</html>
