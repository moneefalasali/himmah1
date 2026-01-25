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
        this.isUploading = false;
    }

    async start() {
        try {
            this.isUploading = true;
            // expose current uploader for external checks
            try { window.WasabiUploaderCurrent = this; } catch(e) {}
            const totalParts = Math.ceil(this.file.size / this.chunkSize);
            
            // 1. Initiate Upload
            // obtain CSRF token safely
            const csrfMeta = document.querySelector('meta[name="csrf-token"]');
            const csrfToken = csrfMeta ? (csrfMeta.getAttribute('content') || csrfMeta.content) : (window.csrfToken || '');

            // Use unified presign endpoint only (centralized under /video/presign)
            const initiateEndpoints = [
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
                '/video/presign/complete'
            ];

            const completeData = await this._tryPostJsonEndpoints(completeEndpoints, {
                uploadId: this.uploadId,
                key: this.key,
                parts: this.parts
            }, csrfToken);
            if (!completeData || !completeData.ok) throw new Error(completeData && completeData.message ? completeData.message : 'Failed to complete upload');

            this.onSuccess(completeData);
            this.isUploading = false;
            try { window.WasabiUploaderCurrent = null; } catch(e) {}
        } catch (error) {
            console.error('Upload failed:', error);
            this.onError(error);
            this.isUploading = false;
            try { window.WasabiUploaderCurrent = null; } catch(e) {}
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
        // Try each endpoint with a few retries for transient failures (e.g., 499)
        for (let ep of endpoints) {
            let attempts = 0;
            while (attempts < 3) {
                attempts++;
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

                    if (!resp.ok) {
                        // Return JSON body on CSRF (419) so caller can surface it
                        if (resp.status === 419) {
                            try { return await resp.json(); } catch (e) { return { ok: false, message: 'Authentication or CSRF error (status 419)'}; }
                        }
                        // For transient client-abort/server-timeout (499) or 502/503, retry
                        if ([499, 502, 503, 504].includes(resp.status) && attempts < 3) {
                            await new Promise(r => setTimeout(r, 500 * attempts));
                            continue;
                        }
                        // otherwise skip to next endpoint
                        break;
                    }

                    if (contentType.includes('application/json')) {
                        return await resp.json();
                    }
                    // non-json successful response: skip
                    break;
                } catch (e) {
                    // network error or thrown - retry a couple times
                    if (attempts < 3) {
                        await new Promise(r => setTimeout(r, 500 * attempts));
                        continue;
                    }
                    console.warn('Endpoint failed', ep, e);
                    break;
                }
            }
        }
        return null;
    }
}

window.WasabiUploader = WasabiUploader;

// helper to check if there's an active upload
window.WasabiUploaderIsUploading = function() {
    try {
        return !!(window.WasabiUploaderCurrent && window.WasabiUploaderCurrent.isUploading);
    } catch(e) { return false; }
};
