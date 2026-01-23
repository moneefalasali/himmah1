/**
 * Wasabi Direct Multipart Upload JS
 * Author: Senior Architect
 */

class WasabiUploader {
    constructor(options) {
        this.file = options.file;
        this.chunkSize = 1024 * 1024 * 5; // 5MB chunks (Wasabi minimum)
        this.onProgress = options.onProgress || (() => {});
        this.onSuccess = options.onSuccess || (() => {});
        this.onError = options.onError || (() => {});
        
        this.uploadId = null;
        this.key = null;
        this.parts = [];
    }

    async start() {
        try {
            const totalParts = Math.ceil(this.file.size / this.chunkSize);
            
            // 1. Initiate Upload
            // obtain CSRF token safely
            const csrfMeta = document.querySelector('meta[name="csrf-token"]');
            const csrfToken = csrfMeta ? (csrfMeta.getAttribute('content') || csrfMeta.content) : (window.csrfToken || '');

            const initiateEndpoints = [
                '/teacher/video/presign/initiate',
                '/admin/video/presign/initiate',
                '/video/presign/initiate'
            ];

            const initData = await this._tryPostJsonEndpoints(initiateEndpoints, {
                filename: this.file.name,
                parts: totalParts,
                content_type: this.file.type
            }, csrfToken);

            if (!initData || !initData.ok) throw new Error(initData && initData.message ? initData.message : 'Failed to initiate upload');

            this.uploadId = initData.uploadId;
            this.key = initData.key;
            const presignedUrls = initData.parts;

            // 2. Upload Parts
            for (let i = 0; i < totalParts; i++) {
                const start = i * this.chunkSize;
                const end = Math.min(start + this.chunkSize, this.file.size);
                const blob = this.file.slice(start, end);
                
                const etag = await this.uploadPart(presignedUrls[i].url, blob, i + 1);
                this.parts.push({
                    PartNumber: i + 1,
                    ETag: etag
                });

                const progress = Math.round(((i + 1) / totalParts) * 100);
                this.onProgress(progress);
            }

            // 3. Complete Upload
            const completeEndpoints = [
                '/teacher/video/presign/complete',
                '/admin/video/presign/complete',
                '/video/presign/complete'
            ];

            const completeData = await this._tryPostJsonEndpoints(completeEndpoints, {
                uploadId: this.uploadId,
                key: this.key,
                parts: this.parts
            }, csrfToken);

            if (!completeData || !completeData.ok) throw new Error(completeData && completeData.message ? completeData.message : 'Failed to complete upload');

            this.onSuccess(completeData);
        } catch (error) {
            console.error('Upload failed:', error);
            this.onError(error);
        }
    }

    async uploadPart(url, blob, partNumber) {
        const response = await fetch(url, {
            method: 'PUT',
            body: blob
        });

        if (!response.ok) throw new Error(`Failed to upload part ${partNumber}`);
        
        // ETag is required for completion
        return response.headers.get('ETag');
    }

    // Try posting to multiple endpoints until one returns JSON success
    async _tryPostJsonEndpoints(endpoints, payload, csrfToken) {
        for (let ep of endpoints) {
            try {
                const resp = await fetch(ep, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(payload)
                });

                const contentType = resp.headers.get('Content-Type') || '';

                // Only accept a JSON response if the HTTP status is successful.
                // If the endpoint returned an error (401/403/4xx/5xx) but with JSON,
                // skip it and try the next endpoint — this allows trying the admin
                // presign endpoint when the teacher endpoint rejects admin users.
                if (!resp.ok) {
                    if (resp.status === 419) {
                        try { return await resp.json(); } catch (e) { return { ok: false, message: 'Authentication or CSRF error (status 419)'}; }
                    }
                    // skip error responses (including JSON error bodies) to try next endpoint
                    continue;
                }

                if (contentType.includes('application/json')) {
                    return await resp.json();
                }
                // non-json successful response: skip
            } catch (e) {
                console.warn('Endpoint failed', ep, e);
                continue;
            }
        }
        return null;
    }
}

window.WasabiUploader = WasabiUploader;
