<div class="photo-choice">
    <h3>Or take a photo</h3>

    <button
        id="open-camera"
        type="button"
        aria-controls="camera-panel"
        aria-expanded="false"
    >
        Open camera
    </button>

    <p
        id="camera-status"
        class="hint"
        role="status"
        aria-live="polite"
    ></p>

    <div id="camera-panel" hidden>
        <video
            id="camera-video"
            autoplay
            muted
            playsinline
            aria-label="Live camera preview"
            style="display: block; width: 100%; max-width: 600px;
                   margin-top: 15px; border-radius: 8px;
                   background: #20231e;"
        ></video>

        <div style="display: flex; flex-wrap: wrap; gap: 12px;">
            <button id="capture-photo" type="button">
                Take photo
            </button>

            <button id="close-camera" type="button">
                Close camera
            </button>
        </div>
    </div>

    {{-- Holds the captured photo for the existing upload form. --}}
    <input
        id="photo-camera"
        type="file"
        accept="image/jpeg,image/png,image/webp"
        capture="environment"
        hidden
    >

    <p class="hint">
        Camera access requires permission.
        You can also choose an existing photo above.
        After taking a photo, click Save photo to upload it.
    </p>
</div>

<script>
    (() => {
        const openButton = document.getElementById('open-camera');
        const captureButton = document.getElementById('capture-photo');
        const closeButton = document.getElementById('close-camera');
        const panel = document.getElementById('camera-panel');
        const video = document.getElementById('camera-video');
        const status = document.getElementById('camera-status');
        const cameraInput = document.getElementById('photo-camera');

        if (
            !openButton
            || !captureButton
            || !closeButton
            || !panel
            || !video
            || !status
            || !cameraInput
        ) {
            return;
        }

        let stream = null;
        let opening = false;
        let capturing = false;

        function stopCamera() {
            if (stream) {
                stream.getTracks().forEach((track) => track.stop());
                stream = null;
            }

            video.srcObject = null;
            panel.hidden = true;
            openButton.setAttribute('aria-expanded', 'false');
        }

        openButton.addEventListener('click', async () => {
            if (opening || stream) {
                return;
            }

            if (
                !window.isSecureContext
                || !navigator.mediaDevices?.getUserMedia
            ) {
                status.textContent =
                    'Camera access needs HTTPS or localhost and a supported browser. '
                    + 'You can choose an existing photo instead.';

                return;
            }

            opening = true;
            openButton.disabled = true;
            status.textContent =
                'Allow camera access when your browser asks.';

            try {
                const cameraStream =
                    await navigator.mediaDevices.getUserMedia({
                        video: {
                            facingMode: { ideal: 'environment' },
                            width: { ideal: 1280 },
                            height: { ideal: 720 },
                        },
                        audio: false,
                    });

                if (document.hidden) {
                    cameraStream.getTracks()
                        .forEach((track) => track.stop());

                    status.textContent =
                        'Return to this tab and click Open camera again.';

                    return;
                }

                stream = cameraStream;
                video.srcObject = stream;

                panel.hidden = false;
                openButton.setAttribute('aria-expanded', 'true');

                await video.play();

                status.textContent =
                    'Camera ready. Position your photo and click Take photo.';
            } catch (error) {
                stopCamera();

                switch (error.name) {
                    case 'NotAllowedError':
                        status.textContent =
                            'Camera permission was declined. '
                            + 'Allow it in your browser settings or choose an existing photo.';
                        break;

                    case 'NotFoundError':
                        status.textContent =
                            'No camera was found. Choose an existing photo instead.';
                        break;

                    case 'NotReadableError':
                        status.textContent =
                            'The camera may be in use by another application. '
                            + 'Close that application and try again.';
                        break;

                    default:
                        status.textContent =
                            'The camera could not start. '
                            + 'Try again or choose an existing photo.';
                }
            } finally {
                opening = false;
                openButton.disabled = false;
            }
        });

        captureButton.addEventListener('click', async () => {
            if (!stream || capturing) {
                return;
            }

            if (!video.videoWidth || !video.videoHeight) {
                status.textContent =
                    'The camera is still starting. Please try again.';
                return;
            }

            capturing = true;
            captureButton.disabled = true;

            try {
                const canvas = document.createElement('canvas');

                // Limit image dimensions to keep uploads manageable.
                const scale = Math.min(
                    1,
                    1600 / Math.max(
                        video.videoWidth,
                        video.videoHeight
                    )
                );

                canvas.width = Math.max(
                    1,
                    Math.round(video.videoWidth * scale)
                );

                canvas.height = Math.max(
                    1,
                    Math.round(video.videoHeight * scale)
                );

                const context = canvas.getContext('2d');

                if (!context) {
                    throw new Error('Could not capture the image.');
                }

                context.drawImage(
                    video,
                    0,
                    0,
                    canvas.width,
                    canvas.height
                );

                const blob = await new Promise((resolve) => {
                    canvas.toBlob(resolve, 'image/jpeg', 0.85);
                });

                if (!blob) {
                    throw new Error('Could not create the photo.');
                }

                const file = new File(
                    [blob],
                    'gigpulse-camera-' + Date.now() + '.jpg',
                    {
                        type: 'image/jpeg',
                        lastModified: Date.now(),
                    }
                );

                const transfer = new DataTransfer();
                transfer.items.add(file);

                cameraInput.files = transfer.files;

                // Reuse the existing photo validation and preview code.
                cameraInput.dispatchEvent(
                    new Event('change', { bubbles: true })
                );

                stopCamera();

                status.textContent =
                    'Photo captured. Check the preview, then click Save photo.';

                document.getElementById('upload-photo')?.focus();
            } catch (error) {
                status.textContent =
                    'The photo could not be captured. '
                    + 'Try again or choose an existing photo.';
            } finally {
                capturing = false;
                captureButton.disabled = false;
            }
        });

        closeButton.addEventListener('click', () => {
            stopCamera();
            status.textContent = 'Camera closed.';
            openButton.focus();
        });

        // Release the camera when the page is left or hidden.
        window.addEventListener('pagehide', stopCamera);

        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                stopCamera();
            }
        });

        cameraInput.form?.addEventListener('submit', stopCamera);
    })();
</script>