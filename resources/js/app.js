import Quill from 'quill';
import 'quill/dist/quill.snow.css';

const carouselSnapTimers = new WeakMap();
const imageCropperState = new WeakMap();

function clamp(value, min, max) {
    return Math.max(min, Math.min(value, max));
}

function updateCarouselDots(carousel, index) {
    carousel?.querySelectorAll('[data-carousel-dot]').forEach((dot, dotIndex) => {
        const active = dotIndex === index;
        dot.classList.toggle('w-6', active);
        dot.classList.toggle('w-2', !active);
        dot.classList.toggle('bg-white', active);
        dot.classList.toggle('bg-white/35', !active);
        dot.classList.toggle('hover:bg-white/70', !active);
        dot.setAttribute('aria-current', active ? 'true' : 'false');
    });
}

function closeLifeModal(modal) {
    modal?.classList.add('hidden');
    modal?.classList.remove('flex');
    document.body.style.overflow = '';
}

function openLifeModal(modal) {
    if (!modal) return;

    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.style.overflow = 'hidden';

    modal.querySelectorAll('[data-carousel]').forEach((carousel) => {
        const scroller = carousel.querySelector('[data-carousel-track]');
        scroller?.scrollTo({ left: 0, behavior: 'auto' });
        updateCarouselDots(carousel, 0);
    });
}

function syncTagPicker(picker) {
    const hidden = picker?.querySelector('[data-tag-hidden]');
    if (!hidden) return;

    hidden.innerHTML = '';
    picker.querySelectorAll('[data-tag-option][aria-pressed="true"]').forEach((option) => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'tags[]';
        input.value = option.value;
        hidden.appendChild(input);
    });
}

function setTagButtonState(button, selected) {
    button.setAttribute('aria-pressed', selected ? 'true' : 'false');
    button.classList.toggle('border-[#5DF8D8]', selected);
    button.classList.toggle('bg-[#5DF8D8]/12', selected);
    button.classList.toggle('text-[#5DF8D8]', selected);
    button.classList.toggle('border-white/10', !selected);
    button.classList.toggle('bg-white/5', !selected);
    button.classList.toggle('text-slate-300', !selected);
    button.classList.toggle('hover:border-[#5DF8D8]/60', !selected);
    button.classList.toggle('hover:text-[#5DF8D8]', !selected);
}

function centerCropBox(width, height, aspectRatio) {
    let cropWidth = width;
    let cropHeight = width / aspectRatio;

    if (cropHeight > height) {
        cropHeight = height;
        cropWidth = height * aspectRatio;
    }

    return {
        x: (width - cropWidth) / 2,
        y: (height - cropHeight) / 2,
        width: cropWidth,
        height: cropHeight,
    };
}

function containedImageRect(stage, item) {
    const stageWidth = stage.clientWidth || 1;
    const stageHeight = stage.clientHeight || 1;
    const scale = Math.min(stageWidth / item.naturalWidth, stageHeight / item.naturalHeight);
    const width = item.naturalWidth * scale;
    const height = item.naturalHeight * scale;

    return {
        scale,
        x: (stageWidth - width) / 2,
        y: (stageHeight - height) / 2,
        width,
        height,
    };
}

function cropperPayload(state) {
    if (state.mode === 'multiple') {
        return JSON.stringify(state.items.map((item) => item.crop));
    }

    return JSON.stringify(state.items[0]?.crop || null);
}

function syncCropperOutput(state) {
    if (state.output) state.output.value = cropperPayload(state);
}

function setCropperThumbs(state) {
    if (!state.thumbs) return;

    state.thumbs.innerHTML = '';

    state.items.forEach((item, index) => {
        const button = document.createElement('button');
        button.type = 'button';
        button.className = `cropper-thumb ${index === state.index ? 'cropper-thumb-active' : ''}`;
        button.textContent = state.mode === 'multiple' ? `Fotoğraf ${index + 1}` : 'Kapak';
        button.addEventListener('click', () => {
            state.index = index;
            renderImageCropper(state);
        });
        state.thumbs.appendChild(button);
    });
}

function renderImageCropper(state) {
    const item = state.items[state.index];
    if (!item) {
        state.panel?.classList.add('hidden');
        syncCropperOutput(state);
        return;
    }

    state.panel?.classList.remove('hidden');
    state.image.src = item.url;

    const rect = containedImageRect(state.stage, item);
    state.image.style.left = `${rect.x}px`;
    state.image.style.top = `${rect.y}px`;
    state.image.style.width = `${rect.width}px`;
    state.image.style.height = `${rect.height}px`;

    state.box.style.left = `${rect.x + item.crop.x * rect.scale}px`;
    state.box.style.top = `${rect.y + item.crop.y * rect.scale}px`;
    state.box.style.width = `${item.crop.width * rect.scale}px`;
    state.box.style.height = `${item.crop.height * rect.scale}px`;

    const maxCrop = centerCropBox(item.naturalWidth, item.naturalHeight, state.aspectRatio);
    state.zoom.value = Math.round((item.crop.width / maxCrop.width) * 100);

    setCropperThumbs(state);
    syncCropperOutput(state);
}

function resizeCropperSelection(state, percent) {
    const item = state.items[state.index];
    if (!item) return;

    const maxCrop = centerCropBox(item.naturalWidth, item.naturalHeight, state.aspectRatio);
    const ratio = clamp(Number(percent) || 100, 30, 100) / 100;
    const centerX = item.crop.x + item.crop.width / 2;
    const centerY = item.crop.y + item.crop.height / 2;
    const width = maxCrop.width * ratio;
    const height = width / state.aspectRatio;

    item.crop = {
        x: clamp(centerX - width / 2, 0, item.naturalWidth - width),
        y: clamp(centerY - height / 2, 0, item.naturalHeight - height),
        width,
        height,
    };

    renderImageCropper(state);
}

function moveCropperSelection(state, deltaX, deltaY) {
    const item = state.items[state.index];
    if (!item) return;

    item.crop = {
        ...item.crop,
        x: clamp(item.crop.x + deltaX, 0, item.naturalWidth - item.crop.width),
        y: clamp(item.crop.y + deltaY, 0, item.naturalHeight - item.crop.height),
    };

    renderImageCropper(state);
}

function loadCropperImage(file, aspectRatio) {
    return new Promise((resolve, reject) => {
        const url = URL.createObjectURL(file);
        const image = new window.Image();
        image.onload = () => {
            resolve({
                file,
                url,
                naturalWidth: image.naturalWidth,
                naturalHeight: image.naturalHeight,
                crop: centerCropBox(image.naturalWidth, image.naturalHeight, aspectRatio),
            });
        };
        image.onerror = () => {
            URL.revokeObjectURL(url);
            reject(new Error(`${file.name} görseli önizleme için açılamadı.`));
        };
        image.src = url;
    });
}

function initImageCroppers() {
    document.querySelectorAll('[data-image-cropper]').forEach((cropper) => {
        if (cropper.dataset.cropperReady === 'true') return;

        const aspectWidth = Number(cropper.dataset.aspectWidth || 1);
        const aspectHeight = Number(cropper.dataset.aspectHeight || 2);
        const state = {
            aspectRatio: aspectWidth / aspectHeight,
            mode: cropper.dataset.cropperMode || 'single',
            input: cropper.querySelector('[data-cropper-input]'),
            output: cropper.querySelector('[data-cropper-output]'),
            panel: cropper.querySelector('[data-cropper-panel]'),
            stage: cropper.querySelector('[data-cropper-stage]'),
            image: cropper.querySelector('[data-cropper-image]'),
            box: cropper.querySelector('[data-cropper-box]'),
            zoom: cropper.querySelector('[data-cropper-zoom]'),
            thumbs: cropper.querySelector('[data-cropper-thumbs]'),
            items: [],
            index: 0,
            drag: null,
        };

        if (!state.input || !state.stage || !state.image || !state.box || !state.zoom) return;

        imageCropperState.set(cropper, state);

        state.input.addEventListener('change', async () => {
            const files = Array.from(state.input.files || []);
            state.index = 0;
            state.items.forEach((item) => URL.revokeObjectURL(item.url));

            if (!files.length) {
                state.items = [];
                renderImageCropper(state);
                return;
            }

            try {
                state.items = await Promise.all(files.map((file) => loadCropperImage(file, state.aspectRatio)));
                renderImageCropper(state);
            } catch (error) {
                state.items = [];
                renderImageCropper(state);
                window.alert(error.message || 'Görseller önizleme için açılamadı.');
            }
        });

        state.zoom.addEventListener('input', () => resizeCropperSelection(state, state.zoom.value));

        state.box.addEventListener('pointerdown', (event) => {
            const item = state.items[state.index];
            if (!item) return;

            const rect = containedImageRect(state.stage, item);
            state.drag = {
                pointerId: event.pointerId,
                x: event.clientX,
                y: event.clientY,
                scale: rect.scale,
            };
            state.box.setPointerCapture(event.pointerId);
        });

        state.box.addEventListener('pointermove', (event) => {
            if (!state.drag || state.drag.pointerId !== event.pointerId) return;

            const deltaX = (event.clientX - state.drag.x) / state.drag.scale;
            const deltaY = (event.clientY - state.drag.y) / state.drag.scale;
            state.drag.x = event.clientX;
            state.drag.y = event.clientY;

            moveCropperSelection(state, deltaX, deltaY);
        });

        state.box.addEventListener('pointerup', () => {
            state.drag = null;
        });

        window.addEventListener('resize', () => renderImageCropper(state));
        cropper.dataset.cropperReady = 'true';
    });
}

function selectQuillImage(quill) {
    const input = document.createElement('input');
    input.type = 'file';
    input.accept = 'image/png,image/jpeg,image/gif,image/webp';
    input.click();

    input.addEventListener('change', () => {
        const file = input.files?.[0];
        if (!file) return;

        const allowedTypes = ['image/png', 'image/jpeg', 'image/gif', 'image/webp'];
        const maxSize = 2 * 1024 * 1024;

        if (!allowedTypes.includes(file.type)) {
            window.alert('Sadece PNG, JPG, GIF veya WebP görsel ekleyebilirsin.');
            return;
        }

        if (file.size > maxSize) {
            window.alert('Görsel boyutu en fazla 2 MB olmalı.');
            return;
        }

        const formData = new FormData();
        formData.append('image', file);

        fetch('/admin/uploads/images', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                Accept: 'application/json',
            },
            body: formData,
        })
            .then(async (response) => {
                const payload = await response.json().catch(() => ({}));

                if (!response.ok) {
                    const details = payload?.errors
                        ? Object.values(payload.errors).flat().join('\n')
                        : payload?.message;
                    throw new Error(details || 'Görsel yüklenemedi. Sunucu dosyayı kabul etmedi.');
                }

                return payload;
            })
            .then(({ url }) => {
                if (!url) throw new Error('Upload failed');
                const range = quill.getSelection(true);
                quill.insertEmbed(range.index, 'image', url, 'user');
                quill.setSelection(range.index + 1, 0, 'silent');
            })
            .catch((error) => window.alert(error.message || 'Görsel yüklenemedi.'));
    });
}

function initQuillEditors() {
    document.querySelectorAll('[data-quill-editor]').forEach((editor) => {
        if (editor.dataset.quillReady === 'true') return;

        const input = document.getElementById(editor.dataset.quillInput);
        const quill = new Quill(editor, {
            theme: 'snow',
            placeholder: editor.dataset.placeholder || '',
            modules: {
                toolbar: {
                    container: [
                        [{ header: [1, 2, 3, false] }],
                        [{ font: [] }, { size: ['small', false, 'large', 'huge'] }],
                        ['bold', 'italic', 'underline', 'strike'],
                        [{ color: [] }, { background: [] }],
                        [{ script: 'sub' }, { script: 'super' }],
                        ['blockquote', 'code-block'],
                        [{ list: 'ordered' }, { list: 'bullet' }, { list: 'check' }],
                        [{ indent: '-1' }, { indent: '+1' }],
                        [{ direction: 'rtl' }, { align: [] }],
                        ['link', 'image', 'video'],
                        ['clean'],
                    ],
                    handlers: {
                        image() {
                            selectQuillImage(this.quill);
                        },
                    },
                },
            },
        });

        quill.clipboard.dangerouslyPasteHTML(input?.value || '', 'silent');
        if (input) input.value = quill.root.innerHTML;

        quill.on('text-change', () => {
            if (input) input.value = quill.root.innerHTML;
        });

        editor.dataset.quillReady = 'true';
    });
}

document.addEventListener('click', (event) => {
    const tagOption = event.target.closest('[data-tag-option]');
    if (tagOption) {
        const picker = tagOption.closest('[data-tag-picker]');
        const selected = tagOption.getAttribute('aria-pressed') !== 'true';

        setTagButtonState(tagOption, selected);
        syncTagPicker(picker);
    }

    const addArrayItem = event.target.closest('[data-array-add]');
    if (addArrayItem) {
        const list = document.getElementById(addArrayItem.dataset.arrayAdd);
        const template = list?.querySelector('template[data-array-template]');

        if (list && template) {
            const fragment = template.content.cloneNode(true);
            const index = `${Date.now()}`;

            fragment.querySelectorAll('[name]').forEach((field) => {
                field.name = field.name.replace('__INDEX__', index);
            });

            list.appendChild(fragment);
        }
    }

    const removeArrayItem = event.target.closest('[data-array-remove]');
    if (removeArrayItem) {
        const row = removeArrayItem.closest('[data-array-row]');
        const list = removeArrayItem.closest('[data-array-list]');
        const visibleRows = list?.querySelectorAll('[data-array-row]');

        if (row && (!visibleRows || visibleRows.length > 1)) {
            row.remove();
        } else {
            row?.querySelectorAll('input, textarea').forEach((input) => {
                input.value = '';
            });
        }
    }

    const toggle = event.target.closest('[data-mobile-toggle]');
    if (toggle) {
        const menu = document.querySelector('[data-mobile-menu]');
        menu?.classList.toggle('hidden');
    }

    if (event.target.matches('[data-life-modal]')) {
        closeLifeModal(event.target);
    }

    const close = event.target.closest('[data-life-close]');
    if (close) {
        closeLifeModal(close.closest('[data-life-modal]'));
    }

    const card = event.target.closest('[data-life-open]');
    if (card) {
        const modal = document.querySelector(`[data-life-modal="${card.dataset.lifeOpen}"]`);
        openLifeModal(modal);
    }

    const dot = event.target.closest('[data-carousel-dot]');
    if (dot) {
        const carousel = dot.closest('[data-carousel]');
        const scroller = carousel?.querySelector('[data-carousel-track]');
        const index = Number(dot.dataset.carouselDot ?? 0);
        if (scroller) {
            scroller.scrollTo({ left: scroller.clientWidth * index, behavior: 'smooth' });
            updateCarouselDots(carousel, index);
        }
    }
});

document.addEventListener('input', (event) => {
    if (event.target.matches('[data-lowercase]')) {
        event.target.value = event.target.value
            .toLowerCase()
            .trim()
            .replace(/\s+/g, '-')
            .replace(/[^a-z0-9-]/g, '');
    }

});

document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
        document.querySelectorAll('[data-life-modal]').forEach(closeLifeModal);
    }
});

document.querySelectorAll('[data-carousel-track]').forEach((track) => {
    let frameId;

    track.addEventListener('scroll', () => {
        window.cancelAnimationFrame(frameId);
        frameId = window.requestAnimationFrame(() => {
            const carousel = track.closest('[data-carousel]');
            const index = Math.round(track.scrollLeft / Math.max(track.clientWidth, 1));
            const safeIndex = Math.max(0, Math.min(index, carousel?.querySelectorAll('[data-carousel-dot]').length - 1 || 0));

            updateCarouselDots(carousel, safeIndex);

            const previousTimer = carouselSnapTimers.get(track);
            if (previousTimer) window.clearTimeout(previousTimer);

            carouselSnapTimers.set(track, window.setTimeout(() => {
                track.scrollTo({ left: track.clientWidth * safeIndex, behavior: 'smooth' });
            }, 120));
        });
    }, { passive: true });
});

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        initQuillEditors();
        initImageCroppers();
    });
} else {
    initQuillEditors();
    initImageCroppers();
}
