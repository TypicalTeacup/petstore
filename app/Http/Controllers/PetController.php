<?php

namespace App\Http\Controllers;

use App\Models\Pet;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class PetController extends Controller
{
    public function index()
    {
        return Pet::with(['category', 'tags'])->get();
    }

    public function create()
    {
        return view('pet.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'min:3'],
            'category.id' => ['numeric', 'exists:categories,id'],
            'status' => ['required', 'in:available,pending,sold'],
            'tags' => ['array'],
            'tags.*.id' => ['required', 'exists:tags,id'],
        ], $request);

        if (($validated instanceof Response)) {
            return $validated;
        }

        $newPet = Pet::create([
            'name' => $validated['name'],
            'category_id' => $validated['category']['id'],
            'status' => $validated['status'],
        ]);
        $newPet->load('category')->makeHidden('category_id');

        $tagIds = array_map(fn($tag) => $tag['id'], $validated['tags']);
        $newPet->tags()->sync($tagIds);

        return $newPet;
    }

    public function storeImage(Request $request, $pet)
    {
        $validated = $this->validate([
            'file' => ['required', 'image'],
            'additionalMetadata' => ['string'],
        ], $request);

        if (($validated instanceof Response)) {
            return $validated;
        }

        $file = $request->file('file');
        $ext = $file->extension();

        $filename = '_' . uniqid();
        if (isset($validated['additionalMetadata'])) {
            $filename = $validated['additionalMetadata'];
        }
        $filename .= '.' . $ext;

        $path = $file->storeAs("img/pet/$pet", $filename, 'public');

        return $this->apiResponse(200, '/storage/' . $path);
    }


    public function findByTags(Request $request)
    {
        $tags = $this->getCSVQueryParam($request, 'tags', true);

        $pets = [];

        foreach ($tags as $tagName) {
            $tag = Tag::whereName($tagName)->first();
            if (!$tag) {
                return $this->apiResponse(400, $tagName);
            }
            $pets = array_merge($pets, $tag->pets
                ->load('category')
                ->makeHidden('category_id')
                ->load('tags')
                ->toArray());
        }

        return $this->apiResponse(200, $pets);
    }

    public function findByStatus(Request $request)
    {
        $statuses = $this->getCSVQueryParam($request, 'status', true);
        $validator = Validator::make($statuses, [
            '*' => 'in:available,pending,sold'
        ]);

        $statuses = $this->validate($validator);

        if ($statuses instanceof Response) {
            return $statuses;
        }

        $pets = Pet::whereIn('status', $statuses)->get()
            ->load('category')
            ->makeHidden('category_id')
            ->load('tags');

        return $this->apiResponse(200, $pets);
    }

    public function show($pet)
    {
        $petModel = Pet::find($pet);
        if (!$petModel) {
            return $this->apiResponse(404, 'not found');
        }

        $petModel
            ->load('category')
            ->makeHidden('category_id')
            ->load('tags');
        return $this->apiResponse(200, $petModel);
    }

    public function edit(Pet $pet)
    {
        return view('pet.edit', ['pet' => $pet]);
    }

    public function update(Request $request)
    {
        $validated = $this->validate([
            'id' => ['numeric', 'gt:0'],
            'name' => ['required', 'string', 'min:3'],
            'category.id' => ['numeric', 'exists:categories,id'],
            'status' => ['required', 'in:available,pending,sold'],
            'tags' => ['array'],
            'tags.*.id' => ['required', 'exists:tags,id'],
        ], $request);

        if (($validated instanceof Response)) {
            return $validated;
        }

        $pet = Pet::find($validated['id']);

        if (!$pet) {
            return $this->apiResponse(404, 'not found');
        }

        $pet->update([
            'name' => $validated['name'],
            'category_id' => $validated['category']['id'],
            'status' => $validated['status'],
        ]);

        $tagIds = array_map(fn($tag) => $tag['id'], $validated['tags']);
        $pet->tags()->sync($tagIds);

        $pet->save();

        return $this->apiResponse(200, strval($pet->id));
    }

    public function updateFormData(Request $request, $petId)
    {
        $validated = $this->validate([
            'name' => ['string', 'min:3'],
            'status' => 'in:available,pending,sold',
        ], $request);

        if ($validated instanceof Response) {
            return $validated;
        }

        $pet = Pet::find($petId);

        if (!$pet) {
            return $this->apiResponse(404, 'not found');
        }

        if (isset($validated['name'])) {
            $pet->name = $validated['name'];
        }
        if (isset($validated['status'])) {
            $pet->status = $validated['status'];
        }

        $pet->save();

        return $this->apiResponse(200, strval($pet->id));
    }

    public function destroy($pet)
    {
        $petModel = Pet::find($pet);
        if (!$petModel) {
            return $this->apiResponse(404, 'not found');
        }
        $petModel->delete();
        return $this->apiResponse(200, strval($petModel->id));
    }
}
