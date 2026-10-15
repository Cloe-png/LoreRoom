<?php
namespace App\Http\Requests;

use App\Models\Place;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use Illuminate\Support\Facades\DB;

abstract class PlaceRequest extends FormRequest
{
    protected function commonRules(): array
    {
        $worldId = (int) session('selected_world_id');
        $sameWorld = function ($table) use ($worldId) { return Rule::exists($table, 'id')->where('world_id', $worldId); };
        return [
            'name' => ['required','string','min:2','max:150'],
            'type' => ['required', Rule::in(['ville','village','forteresse','foret','montagne','taverne','temple','ruines','region','royaume','continent','dimension','autre'])],
            'short_description' => ['required','string','max:280'],
            'parent_id' => ['nullable','integer',$sameWorld('places')],
            'description' => ['nullable','string','max:30000'], 'climate' => ['nullable','string','max:255'],
            'population' => ['nullable','integer','min:0'], 'atmosphere' => ['nullable','array'], 'atmosphere.*' => ['string','max:40'],
            'alt_names' => ['nullable','array'], 'alt_names.*' => ['string','max:150'],
            'government' => ['nullable','string','max:255'], 'religion' => ['nullable','string','max:255'],
            'languages' => ['nullable','array'], 'languages.*' => ['string','max:100'],
            'economy' => ['nullable','string','max:12000'], 'dangers' => ['nullable','string','max:12000'],
            'founded_at' => ['nullable','string','max:255'], 'history' => ['nullable','string','max:20000'],
            'private_notes' => ['nullable','string','max:20000'],
            'status' => ['sometimes', Rule::in(['prospere','en_ruine','disparu','cache','conteste','inconnu'])],
            'visibility' => ['sometimes', Rule::in(['brouillon','publie','secret'])],
            'image' => ['nullable','image','mimes:jpg,jpeg,png,webp','max:5120'],
        ] + $this->relationRules($sameWorld);
    }

    private function relationRules(callable $sameWorld): array
    {
        $rules = [];
        foreach (['connected_places'=>'places','leaders'=>'characters','factions'=>'factions','species'=>'species','characters'=>'characters','chronicles'=>'chronicles','chapters'=>'chapters'] as $key=>$table) {
            $rules[$key] = ['nullable','array']; $rules[$key.'.*'] = ['integer',$sameWorld($table)];
        }
        $rules['travel_notes'] = ['nullable','array']; $rules['travel_notes.*'] = ['nullable','string','max:255'];
        $rules['leader_titles'] = ['nullable','array']; $rules['leader_titles.*'] = ['nullable','string','max:120'];
        $rules['points'] = ['nullable','array']; $rules['points.*.name'] = ['required','string','max:150']; $rules['points.*.description'] = ['nullable','string','max:2000'];
        return $rules;
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $name = trim((string) $this->input('name'));
            if ($name !== '') {
                $query = DB::table('places')->where('world_id', (int) session('selected_world_id'))->whereRaw('LOWER(name) = ?', [mb_strtolower($name)]);
                $current = $this->route('place');
                if ($current) $query->where('id', '!=', $current->id);
                if ($query->exists()) $validator->errors()->add('name', 'Un lieu portant ce nom existe déjà dans ce monde.');
            }
            $parentId = (int) $this->input('parent_id');
            if (!$parentId) return;
            $current = $this->route('place');
            if ($current && $parentId === (int) $current->id) { $validator->errors()->add('parent_id', 'Un lieu ne peut pas être son propre parent.'); return; }
            $ancestor = Place::query()->find($parentId);
            $seen = [];
            while ($ancestor) {
                if ($current && (int) $ancestor->id === (int) $current->id) { $validator->errors()->add('parent_id', 'Cette hiérarchie créerait une boucle.'); return; }
                if (isset($seen[$ancestor->id])) { $validator->errors()->add('parent_id', 'La hiérarchie existante contient une boucle.'); return; }
                $seen[$ancestor->id] = true; $ancestor = $ancestor->parent;
            }
        });
    }
}
