@extends('manage.layout')

@section('title', 'Gestion - Éditer lieu')
@section('header', 'Éditer lieu')

@section('content')
    <section class="panel">
        <form method="POST" action="{{ route('manage.places.update', $place) }}" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="field">
                <label>Monde</label>
                <input type="text" value="{{ optional($defaultWorld)->name ?: 'Monde unique' }}" disabled>
            </div>
            <div class="field">
                <label>Nom</label>
                <input type="text" name="name" value="{{ old('name', $place->name) }}" required>
            </div>
            <div class="field">
                <label>Type de lieu</label>
                <select name="type">
                    <option value="">Non défini</option>
                    @foreach($placeTypeOptions as $typeOption)
                        <option value="{{ $typeOption }}" {{ old('type', $place->type) === $typeOption ? 'selected' : '' }}>{{ ucfirst($typeOption) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field">
                <label>Région</label>
                <input type="text" name="region" value="{{ old('region', $place->region) }}">
            </div>

            <div class="field">
                <label>Image principale</label>
                <input type="file" name="image" accept=".jpg,.png,image/jpeg,image/png">
                @if($place->image_path)
                    <p class="muted" style="margin-top:8px;">Image actuelle:</p>
                    <img src="{{ route('media.show', ['path' => $place->image_path]) }}" alt="image" style="max-width:220px; border-radius:8px; border:1px solid rgba(101,74,42,.28);">
                @endif
            </div>

            <div class="field">
                <label>Résumé</label>
                <textarea name="summary">{{ old('summary', $place->summary) }}</textarea>
            </div>

            <details class="accordion" open>
                <summary>Galerie d'images</summary>
                <div class="accordion-body">
                    @if($place->galleryImages->isNotEmpty())
                        <div class="panel" style="margin-top:0;">
                            <strong>Images existantes</strong>
                            <div class="grid-4" style="margin-top:8px;">
                                @foreach($place->galleryImages as $img)
                                    <div class="card" style="padding:8px;">
                                        <img src="{{ route('media.show', ['path' => $img->image_path]) }}" alt="image" style="width:100%; border-radius:6px; margin-bottom:6px;">
                                        <div class="muted">{{ $img->caption ?: 'Sans légende' }}</div>
                                        <label><input type="checkbox" name="remove_gallery_ids[]" value="{{ $img->id }}"> Supprimer</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div id="gallery-list">
                        <div class="panel" data-gallery-row style="margin-top:10px; padding:10px;">
                            <div class="grid-4">
                                <div class="field" style="grid-column: span 2;"><label>Image</label><input type="file" name="gallery_images[]" accept=".jpg,.png,image/jpeg,image/png"></div>
                                <div class="field" style="grid-column: span 2;"><label>Légende</label><input type="text" name="gallery_captions[]" value=""></div>
                            </div>
                            <button class="btn danger" type="button" data-remove-gallery>Retirer</button>
                        </div>
                    </div>
                    <div class="stack" style="margin-bottom:10px;"><button class="btn secondary" type="button" id="add-gallery-btn">Ajouter une image</button></div>
                </div>
            </details>

            <div class="stack">
                <button class="btn" type="submit">Enregistrer</button>
                <a class="btn secondary" href="{{ route('manage.places.index') }}">Retour</a>
            </div>
        </form>
    </section>

    <template id="gallery-row-template">
        <div class="panel" data-gallery-row style="margin-top:10px; padding:10px;">
            <div class="grid-4">
                <div class="field" style="grid-column: span 2;"><label>Image</label><input type="file" name="gallery_images[]" accept=".jpg,.png,image/jpeg,image/png"></div>
                <div class="field" style="grid-column: span 2;"><label>Légende</label><input type="text" name="gallery_captions[]" value=""></div>
            </div>
            <button class="btn danger" type="button" data-remove-gallery>Retirer</button>
        </div>
    </template>

    <script>
        (function () {
            const galleryList = document.getElementById('gallery-list');
            const addGalleryBtn = document.getElementById('add-gallery-btn');
            const galleryTpl = document.getElementById('gallery-row-template');
            if (!galleryList || !addGalleryBtn || !galleryTpl) return;

            function bindGalleryRemove() {
                galleryList.querySelectorAll('[data-remove-gallery]').forEach((btn) => {
                    btn.onclick = function () {
                        const rows = galleryList.querySelectorAll('[data-gallery-row]');
                        if (rows.length <= 1) return;
                        btn.closest('[data-gallery-row]').remove();
                    };
                });
            }

            addGalleryBtn.addEventListener('click', function () {
                galleryList.appendChild(galleryTpl.content.firstElementChild.cloneNode(true));
                bindGalleryRemove();
            });

            bindGalleryRemove();
        })();
    </script>
@endsection
