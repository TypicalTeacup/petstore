<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite(['resources/css/app.css'])
    <title></title>
</head>
<body class="m-4 container mx-auto">
    <div class="flex mb-4 justify-center items-center gap-4">
        <h1 class="font-bold text-4xl">Zwierzęta</h1>
        <a href="/pet/{{$pet}}/edit" class="underline text-blue-800 rounded-sm">Edytuj</a>
        <button onclick="deletePet()" class="underline text-red-500 rounded-sm">Usuń</button>
    </div>
    <p id="category"></p>
    <p id="tags"></p>
    <p id="status"></p>
    <div class="mt-4 flex items-center gap-4">
        <h2 class="font-semibold text-2xl">Zdjęcia</h2>
        <button class="border bg-gray-400 p-1 rounded" onclick="uploadPhoto()">Dodaj zdjęcie</button>
    </div>
    <div id="photos"></div>
</body>
<script>
    fetch('/api/pet/{{$pet}}').then(r=>r.json()).then(data=>{
        document.querySelector('h1').innerText = data.body.name;
        document.querySelector('#category').innerText = `Kategoria: ${data.body.category.name}`;
        document.querySelector('#tags').innerText = `Tagi: ${data.body.tags.map(tag=>tag.name).join(', ')}`;
        document.querySelector('#status').innerText = `Status: ${data.body.status}`;
        document.querySelector('title').innerText = data.body.name;

        data.body.photoUrls.forEach(photo=> {
            const img = document.createElement('img');
            img.src = photo;
            document.querySelector('#photos').appendChild(img);
        });
    });

    function uploadPhoto() {
        const formData = new FormData();
        const input = document.createElement('input');
        input.type = 'file';
        input.accept = 'image/*';
        input.onchange = (e) => {
            formData.append('file', e.target.files[0]);
            const alt = prompt('Podaj opis zdjęcia');
            formData.append('additionalMetadata', alt);
            fetch(`/api/pet/{{$pet}}/uploadImage`, {
                method: 'POST',
                body: formData
            }).then(r=>r.json()).then(data=>{
                const img = document.createElement('img');
                img.src = data.body;
                document.querySelector('#photos').appendChild(img);
            });
        };
        input.click();
    }

    function deletePet() {
        fetch(`/api/pet/{{$pet}}`, {
            method: 'DELETE'
        }).then(r=>r.json()).then(data=>{
            if (data.code === 200) {
                window.location.href = '/pet';
            }
        });
    }
</script>
</html>
