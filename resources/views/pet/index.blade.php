<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    @vite(['resources/css/app.css'])
    <title>Zwierzęta</title>
</head>
<body class="m-4">
    <div class="flex mb-4 justify-center items-center gap-4">
        <h1 class="font-bold text-4xl">Zwierzęta</h1>
        <a href="/pet/new" class="underline text-blue-800 rounded-sm">Dodaj</a>
    </div>
    <div id="petsList"></div>
</body>
<script>
    const petsList = document.getElementById('petsList');
    fetch('/api/pet', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
    }).then(response => response.json())
        .then(data => {
            data.forEach(pet => {
                const petElement = document.createElement('div');
                petElement.classList.add('border', 'border-black', 'p-4', 'mb-4');
                petElement.innerHTML = `
                    <div class="flex gap-4 mb-1">
                        <h2 class="text-2xl font-semibold">${pet.name}</h2>
                        <button onclick="deletePet(${pet.id})" class="underline text-red-500 rounded-sm">Usuń</button>
                    </div>
                    <p>Kategoria: ${pet.category.name}</p>
                    <p>Status: ${pet.status}</p>
                    <p class="mb-1">Tagi: ${pet.tags.map(tag => tag.name).join(', ')}</p>
                    <a class="underline text-blue-800" href="/pet/${pet.id}">Szczegóły</a>
                `;
                petsList.appendChild(petElement);
            });
        });

    function deletePet(id) {
        fetch(`/api/pet/${id}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        }).then(r=>r.json())
            .then(response => {
            if (response.code === 200) {
                window.location.reload();
            }
        });
    }
</script>
</html>
