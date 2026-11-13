import { toJpeg, toPng } from 'html-to-image';

const PAPER = '#efe2c9';
const MAX_SIDE = 8000;

function slug(value) {
    return String(value || '')
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .toLowerCase()
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-+|-+$/g, '') || 'export';
}

function fallbackImage(image) {
    const box = document.createElement('div');
    box.className = 'image-export-silhouette';
    box.style.width = image.style.width || (image.classList.contains('portrait') ? '180px' : '160px');
    box.style.height = image.style.height || (image.classList.contains('portrait') ? '240px' : '130px');
    box.setAttribute('aria-label', 'Image indisponible, silhouette de remplacement');
    box.innerHTML = '<svg viewBox="0 0 120 160" aria-hidden="true"><circle cx="60" cy="42" r="27" fill="#9b8060"/><path d="M15 150c2-39 19-61 45-61s43 22 45 61" fill="#9b8060"/></svg>';
    image.replaceWith(box);
}

async function waitForImages(root) {
    await Promise.all(Array.from(root.querySelectorAll('img')).map((image) => new Promise((resolve) => {
        const done = () => {
            if (!image.naturalWidth) fallbackImage(image);
            resolve();
        };
        if (image.complete) {
            done();
            return;
        }
        image.addEventListener('load', done, { once: true });
        image.addEventListener('error', done, { once: true });
        window.setTimeout(done, 8000);
    })));
}

async function exportElement({ node, format, filename, scale = 2, button, status }) {
    if (!node) throw new Error('La vue à exporter est introuvable.');
    const originalText = button ? button.textContent : '';
    if (button) {
        button.disabled = true;
        button.textContent = 'Export en cours…';
    }
    if (status) {
        status.textContent = '';
        status.hidden = true;
    }

    try {
        if (document.fonts && document.fonts.ready) await document.fonts.ready;
        await waitForImages(node);
        const width = Math.max(node.scrollWidth, node.offsetWidth, 1);
        const height = Math.max(node.scrollHeight, node.offsetHeight, 1);
        const pixelRatio = Math.min(Number(scale) || 2, MAX_SIDE / width, MAX_SIDE / height);
        if (pixelRatio < (Number(scale) || 2) && status) {
            status.hidden = false;
            status.textContent = 'La grande taille de cette fiche a réduit l’échelle pour respecter la limite de 8 000 pixels.';
        }
        const options = { pixelRatio, backgroundColor: PAPER, cacheBust: true };
        const dataUrl = format === 'jpg'
            ? await toJpeg(node, { ...options, quality: 0.92 })
            : await toPng(node, options);
        const link = document.createElement('a');
        link.download = `${filename}.${format === 'jpg' ? 'jpg' : 'png'}`;
        link.href = dataUrl;
        document.body.appendChild(link);
        link.click();
        link.remove();
    } finally {
        if (button) {
            button.disabled = false;
            button.textContent = originalText;
        }
    }
}

window.LoreRoomImageExport = { exportElement, slug, paper: PAPER };

document.addEventListener('DOMContentLoaded', () => {
    const buttons = Array.from(document.querySelectorAll('[data-character-export-format]'));
    if (!buttons.length) return;
    const host = document.getElementById('character-export-host');
    const status = document.getElementById('character-export-status');
    let canvas = null;
    let loading = null;

    async function loadCanvas() {
        if (canvas) return canvas;
        if (loading) return loading;
        loading = fetch(host.dataset.exportView, { credentials: 'same-origin' })
            .then((response) => {
                if (!response.ok) throw new Error('La fiche n’a pas pu être préparée pour l’export.');
                return response.text();
            })
            .then((html) => {
                const parsed = new DOMParser().parseFromString(html, 'text/html');
                const source = parsed.querySelector('[data-image-export-canvas]');
                if (!source) throw new Error('La vue d’export ne contient pas la fiche.');
                parsed.querySelectorAll('style').forEach((style) => host.appendChild(style.cloneNode(true)));
                canvas = source;
                host.appendChild(canvas);
                return canvas;
            });
        return loading;
    }

    buttons.forEach((button) => button.addEventListener('click', async () => {
        const originalText = button.textContent;
        button.disabled = true;
        button.textContent = 'Export en cours…';
        try {
            const node = await loadCanvas();
            const name = window.LoreRoomImageExport.slug(host.dataset.characterName);
            const world = window.LoreRoomImageExport.slug(host.dataset.worldName);
            await exportElement({
                node,
                format: button.dataset.characterExportFormat,
                filename: `${name}-${world}`,
                scale: Number(host.dataset.exportScale) || 2,
                button: null,
                status,
            });
        } catch (error) {
            status.hidden = false;
            status.textContent = error && error.message ? error.message : 'L’export de l’image a échoué.';
        } finally {
            button.disabled = false;
            button.textContent = originalText;
        }
    }));
});
