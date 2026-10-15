<?php
namespace App\Http\Requests;
use App\Models\Place;
class UpdatePlaceRequest extends PlaceRequest
{
    public function authorize(): bool
    {
        $place = $this->route('place');
        if ((int) $place->world_id !== (int) session('selected_world_id') || !$this->user()->worlds()->whereKey($place->world_id)->exists()) abort(404);
        return $this->user()->can('update', $place);
    }
    public function rules(): array { return $this->commonRules(); }
    public function messages(): array { return ['name.required'=>'Le nom du lieu est obligatoire.','name.min'=>'Le nom doit contenir au moins 2 caractères.','short_description.required'=>'La description courte est obligatoire.','parent_id.exists'=>'Le lieu parent doit appartenir au monde actif.','image.max'=>'L’image ne peut pas dépasser 5 Mo.']; }
}
