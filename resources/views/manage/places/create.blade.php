@extends('manage.layout')

@section('title', 'Gestion - Nouveau lieu')
@section('header', 'Nouveau lieu')

@section('content')
    <section class="panel">
        <form method="POST" action="{{ route('manage.places.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="field">
                <label>Monde</label>
                <input type="text" value="{{ optional($defaultWorld)->name ?: 'Monde unique' }}" disabled>
            </div>
            <div class="field">
                <label>Nom</label>
                <input type="text" name="name" value="{{ old('name') }}" required>
            </div>
            <div class="field">
                <label>Type de lieu</label>
                <select name="type">
                    <option value="">Non defini</option>
                    @foreach($placeTypeOptions as $typeOption)
                        <option value="{{ $typeOption }}" {{ old('type') === $typeOption ? 'selected' : '' }}>{{ ucfirst($typeOption) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field">
                <label>Region</label>
                <input type="text" name="region" value="{{ old('region') }}">
            </div>

            <div class="field">
                <label>Image principale</label>
                <input type="file" name="image" accept="image/*">
            </div>

            <div class="field">
                <label>Resume</label>
                <textarea name="summary">{{ old('summary') }}</textarea>
            </div>

            <details class="accordion" open>
                <summary>Galerie d'images</summary>
                <div class="accordion-body">
                    <div id="gallery-list">
                        <div class="panel" data-gallery-row style="margin-top:10px; padding:10px;">
                            <div class="grid-4">
                                <div class="field" style="grid-column: span 2;"><label>Image</label><input type="file" name="gallery_images[]" accept="image/*"></div>
                                <div class="field" style="grid-column: span 2;"><label>Legende</label><input type="text" name="gallery_captions[]" value=""></div>
                            </div>
                            <button class="btn danger" type="button" data-remove-gallery>Retirer</button>
                        </div>
                    </div>
                    <div class="stack" style="margin-bottom:10px;"><button class="btn secondary" type="button" id="add-gallery-btn">Ajouter une image</button></div>
                </div>
            </details>

            <div class="stack">
                <button class="btn" type="submit">Créer</button>
                <a class="btn secondary" href="{{ route('manage.places.index') }}">Annuler</a>
            </div>
        </form>
    </section>

    <template id="gallery-row-template">
        <div class="panel" data-gallery-row style="margin-top:10px; padding:10px;">
            <div class="grid-4">
                <div class="field" style="grid-column: span 2;"><label>Image</label><input type="file" name="gallery_images[]" accept="image/*"></div>
                <div class="field" style="grid-column: span 2;"><label>Legende</label><input type="text" name="gallery_captions[]" value=""></div>
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
