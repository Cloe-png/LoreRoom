<?php
namespace App\Http\Requests;
use App\Models\Place;
class StorePlaceRequest extends PlaceRequest
{
    public function authorize(): bool { return $this->user()->can('create', Place::class); }
    public function rules(): array { return $this->commonRules(); }
    public function messages(): array { return ['name.required'=>'Le nom du lieu est obligatoire.','name.min'=>'Le nom doit contenir au moins 2 caractères.','short_description.required'=>'La description courte est obligatoire.','parent_id.exists'=>'Le lieu parent doit appartenir au monde actif.','image.max'=>'L’image ne peut pas dépasser 5 Mo.']; }
}
