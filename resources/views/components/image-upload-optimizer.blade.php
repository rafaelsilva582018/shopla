@once
    <script>
        (() => {
            const MAX_WIDTH = 1600;
            const MAX_HEIGHT = 1600;
            const TARGET_BYTES = 1.5 * 1024 * 1024;

            const canvasToBlob = (canvas, type, quality) => new Promise((resolve) => {
                canvas.toBlob(resolve, type, quality);
            });

            const buildImageBlob = async (image, maxWidth, maxHeight, quality) => {
                const scale = Math.min(1, maxWidth / image.width, maxHeight / image.height);
                const width = Math.max(1, Math.round(image.width * scale));
                const height = Math.max(1, Math.round(image.height * scale));

                const canvas = document.createElement('canvas');
                canvas.width = width;
                canvas.height = height;

                const context = canvas.getContext('2d');
                context.drawImage(image, 0, 0, width, height);

                return await canvasToBlob(canvas, 'image/webp', quality)
                    || await canvasToBlob(canvas, 'image/jpeg', quality);
            };

            const optimizeFile = async (file) => {
                if (!file || !file.type.startsWith('image/')) {
                    return file;
                }

                const image = new Image();
                const objectUrl = URL.createObjectURL(file);

                try {
                    await new Promise((resolve, reject) => {
                        image.onload = resolve;
                        image.onerror = reject;
                        image.src = objectUrl;
                    });

                    let maxWidth = MAX_WIDTH;
                    let maxHeight = MAX_HEIGHT;
                    let bestBlob = null;
                    let bestType = 'image/webp';

                    for (let attempt = 0; attempt < 5; attempt++) {
                        for (const quality of [0.82, 0.72, 0.62, 0.52]) {
                            const blob = await buildImageBlob(image, maxWidth, maxHeight, quality);

                            if (blob && (!bestBlob || blob.size < bestBlob.size)) {
                                bestBlob = blob;
                                bestType = blob.type || bestType;
                            }

                            if (blob && blob.size <= TARGET_BYTES) {
                                bestBlob = blob;
                                bestType = blob.type || bestType;
                                break;
                            }
                        }

                        if (bestBlob && bestBlob.size <= TARGET_BYTES) {
                            break;
                        }

                        maxWidth = Math.round(maxWidth * 0.82);
                        maxHeight = Math.round(maxHeight * 0.82);
                    }

                    if (!bestBlob || (file.size <= TARGET_BYTES && bestBlob.size >= file.size)) {
                        return file;
                    }

                    const extension = bestType === 'image/webp' ? 'webp' : 'jpg';
                    const filename = file.name.replace(/\.[^.]+$/, '') + '.' + extension;

                    return new File([bestBlob], filename, {
                        type: bestType,
                        lastModified: Date.now(),
                    });
                } finally {
                    URL.revokeObjectURL(objectUrl);
                }
            };

            document.addEventListener('submit', async (event) => {
                const form = event.target.closest('form[data-optimize-images]');

                if (!form || form.dataset.optimizedImages === 'true') {
                    return;
                }

                const inputs = [...form.querySelectorAll('input[type="file"]')]
                    .filter((input) => input.files && input.files.length);

                if (!inputs.length) {
                    return;
                }

                event.preventDefault();

                const submitButton = form.querySelector('button[type="submit"], button:not([type]), input[type="submit"]')
                    || (form.id ? document.querySelector(`button[form="${form.id}"], input[form="${form.id}"]`) : null);
                const originalText = submitButton?.textContent;

                if (submitButton) {
                    submitButton.disabled = true;
                    submitButton.textContent = 'Otimizando imagem...';
                }

                try {
                    for (const input of inputs) {
                        const optimized = await optimizeFile(input.files[0]);
                        const transfer = new DataTransfer();
                        transfer.items.add(optimized);
                        input.files = transfer.files;
                    }

                    form.dataset.optimizedImages = 'true';
                    form.requestSubmit();
                } catch (error) {
                    alert('Não foi possível otimizar a imagem. Tente outra foto.');

                    if (submitButton) {
                        submitButton.disabled = false;
                        submitButton.textContent = originalText;
                    }
                }
            }, true);
        })();
    </script>
@endonce
