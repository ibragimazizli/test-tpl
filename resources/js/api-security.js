/**
 * API Security Helper
 *
 * Generates required security headers for API requests
 * - Request signatures (HMAC-SHA256)
 * - Browser fingerprinting
 * - Nonce generation
 * - Timestamp management
 */

import CryptoJS from 'crypto-js';

class ApiSecurityHelper {
    constructor() {
        this.secret = null;
        this.fingerprint = null;
        this.initialized = false;
    }

    /**
     * Initialize security helper
     */
    async init() {
        if (this.initialized) return;

        // Generate browser fingerprint
        this.fingerprint = await this.generateFingerprint();

        // Get secret from server (embedded in page)
        this.secret = window.API_SECURITY_SECRET || this.getMetaContent('api-security-secret');

        this.initialized = true;
    }

    /**
     * Generate browser fingerprint
     * Uses multiple browser characteristics to create unique ID
     */
    async generateFingerprint() {
        try {
            // Check for persistent UUID or create one
            let deviceUuid = localStorage.getItem('api_sec_device_uid');
            if (!deviceUuid) {
                deviceUuid = 'uid_' + Date.now().toString(36) + Math.random().toString(36).substring(2);
                try {
                    localStorage.setItem('api_sec_device_uid', deviceUuid);
                } catch (e) { } // Ignore incognito quota errors
            }

            const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) || (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);

            const components = {
                // Persistent UUID
                deviceId: deviceUuid,

                // Screen properties (avoiding screenAvail as it fluctuates wildly on mobile scroll)
                screen: `${screen.width}x${screen.height}x${screen.colorDepth}`,

                // Timezone
                timezone: Intl.DateTimeFormat().resolvedOptions().timeZone,
                timezoneOffset: new Date().getTimezoneOffset(),

                // Language
                language: navigator.language,

                // Platform
                platform: navigator.platform || navigator.userAgentData?.platform || 'unknown',
                userAgent: navigator.userAgent,

                // Hardware
                hardwareConcurrency: navigator.hardwareConcurrency || 0,
                deviceMemory: navigator.deviceMemory || 0,

                // Plugins (deprecated but still useful)
                plugins: this.getPluginsHash(),

                // Canvas & Audio fingerprinting (skipped for iOS due to extreme anti-tracking noise rendering variations)
                canvas: isIOS ? 'ios_skipped' : await this.getCanvasFingerprint(),
                webgl: this.getWebGLFingerprint(),
                fonts: isIOS ? 'ios_skipped' : await this.getFontsFingerprint(),
                audio: isIOS ? 'ios_skipped' : await this.getAudioFingerprint(),
            };

            // Combine all components
            const fingerprintString = JSON.stringify(components);

            // Hash to create fingerprint
            return await this.sha256(fingerprintString);
        } catch (e) {
            // Fallback: generate a fingerprint from basic properties only
            // This ensures we always return a valid 64-char hex string
            let fallbackUuid = '';
            try {
                fallbackUuid = localStorage.getItem('api_sec_device_uid') || '';
                if (!fallbackUuid) {
                    fallbackUuid = 'uid_' + Date.now().toString(36) + Math.random().toString(36).substring(2);
                    localStorage.setItem('api_sec_device_uid', fallbackUuid);
                }
            } catch (storageErr) { } // Ignore incognito quota errors

            const fallback = [
                fallbackUuid,
                screen.width, screen.height, screen.colorDepth,
                navigator.language, navigator.userAgent,
                new Date().getTimezoneOffset(),
                navigator.hardwareConcurrency || 0
            ].join('|');
            return await this.sha256(fallback);
        }
    }

    /**
     * Get plugins hash
     */
    getPluginsHash() {
        if (!navigator.plugins || navigator.plugins.length === 0) {
            return 'none';
        }

        const plugins = Array.from(navigator.plugins)
            .map(p => p.name)
            .sort()
            .join(',');

        return plugins;
    }

    /**
     * Generate canvas fingerprint
     */
    async getCanvasFingerprint() {
        try {
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');

            if (!ctx) return 'unsupported';

            canvas.width = 200;
            canvas.height = 50;

            // Draw text with specific styling
            ctx.textBaseline = 'top';
            ctx.font = '14px "Arial"';
            ctx.textBaseline = 'alphabetic';
            ctx.fillStyle = '#f60';
            ctx.fillRect(125, 1, 62, 20);
            ctx.fillStyle = '#069';
            ctx.fillText('ISB.az Security', 2, 15);
            ctx.fillStyle = 'rgba(102, 204, 0, 0.7)';
            ctx.fillText('ISB.az Security', 4, 17);

            // Get canvas data
            const dataUrl = canvas.toDataURL();
            return await this.sha256(dataUrl);
        } catch (e) {
            return 'error';
        }
    }

    /**
     * Generate WebGL fingerprint
     */
    getWebGLFingerprint() {
        try {
            const canvas = document.createElement('canvas');
            const gl = canvas.getContext('webgl') || canvas.getContext('experimental-webgl');

            if (!gl) return 'unsupported';

            const debugInfo = gl.getExtension('WEBGL_debug_renderer_info');
            if (!debugInfo) return 'no-debug-info';

            const vendor = gl.getParameter(debugInfo.UNMASKED_VENDOR_WEBGL);
            const renderer = gl.getParameter(debugInfo.UNMASKED_RENDERER_WEBGL);

            return `${vendor}~${renderer}`;
        } catch (e) {
            return 'error';
        }
    }

    /**
     * Generate fonts fingerprint
     */
    async getFontsFingerprint() {
        const baseFonts = ['monospace', 'sans-serif', 'serif'];
        const testFonts = [
            'Arial', 'Verdana', 'Times New Roman', 'Courier New',
            'Georgia', 'Palatino', 'Garamond', 'Bookman', 'Comic Sans MS',
            'Trebuchet MS', 'Impact'
        ];

        const detected = [];

        for (const font of testFonts) {
            if (this.isFontAvailable(font, baseFonts)) {
                detected.push(font);
            }
        }

        return detected.join(',');
    }

    /**
     * Check if font is available
     */
    isFontAvailable(font, baseFonts) {
        const testString = 'mmmmmmmmmmlli';
        const testSize = '72px';
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');

        if (!ctx) return false;

        const baseWidths = {};
        for (const baseFont of baseFonts) {
            ctx.font = `${testSize} ${baseFont}`;
            baseWidths[baseFont] = ctx.measureText(testString).width;
        }

        for (const baseFont of baseFonts) {
            ctx.font = `${testSize} ${font}, ${baseFont}`;
            const width = ctx.measureText(testString).width;

            if (width !== baseWidths[baseFont]) {
                return true;
            }
        }

        return false;
    }

    /**
     * Generate audio fingerprint
     */
    async getAudioFingerprint() {
        try {
            const OfflineCtx = window.OfflineAudioContext || window.webkitOfflineAudioContext;
            if (!OfflineCtx) return 'unsupported';

            const context = new OfflineCtx(1, 44100, 44100); // 1 second of audio
            const oscillator = context.createOscillator();
            const compressor = context.createDynamicsCompressor();

            // Configure oscillator
            oscillator.type = 'triangle';
            oscillator.frequency.setValueAtTime(10000, context.currentTime);

            // Configure compressor for unique processing
            compressor.threshold.setValueAtTime(-50, context.currentTime);
            compressor.knee.setValueAtTime(40, context.currentTime);
            compressor.ratio.setValueAtTime(12, context.currentTime);
            compressor.attack.setValueAtTime(0, context.currentTime);
            compressor.release.setValueAtTime(0.25, context.currentTime);

            oscillator.connect(compressor);
            compressor.connect(context.destination);
            oscillator.start(0);

            // Render with timeout
            const buffer = await Promise.race([
                context.startRendering(),
                new Promise((_, reject) => setTimeout(() => reject('timeout'), 1000))
            ]);

            // Extract fingerprint from rendered audio data
            const channelData = buffer.getChannelData(0);
            const hash = Array.from(channelData.slice(4500, 4530))
                .map(v => v.toFixed(6))
                .join(',');

            return hash;
        } catch (e) {
            return 'unsupported';
        }
    }

    /**
     * Generate SHA-256 hash
     * Falls back to a simple hash if crypto.subtle is unavailable (non-HTTPS on iOS Safari)
     */
    async sha256(message) {
        // crypto.subtle is only available in secure contexts (HTTPS)
        if (typeof crypto !== 'undefined' && crypto.subtle && crypto.subtle.digest) {
            try {
                const msgBuffer = new TextEncoder().encode(message);
                const hashBuffer = await crypto.subtle.digest('SHA-256', msgBuffer);
                const hashArray = Array.from(new Uint8Array(hashBuffer));
                return hashArray.map(b => b.toString(16).padStart(2, '0')).join('');
            } catch (e) {
                // Fall through to fallback
            }
        }

        // Fallback using CryptoJS
        return CryptoJS.SHA256(message).toString(CryptoJS.enc.Hex);
    }

    /**
     * Generate HMAC-SHA256 signature
     */
    async generateSignature(method, path, timestamp, nonce, body) {
        const payload = `${method}|${path}|${timestamp}|${nonce}|${body}`;

        if (typeof crypto !== 'undefined' && crypto.subtle && crypto.subtle.importKey && crypto.subtle.sign) {
            try {
                const encoder = new TextEncoder();

                // Decode base64 secret to binary bytes
                const base64ToBytes = (base64) => {
                    const binaryString = atob(base64);
                    const bytes = new Uint8Array(binaryString.length);
                    for (let i = 0; i < binaryString.length; i++) {
                        bytes[i] = binaryString.charCodeAt(i);
                    }
                    return bytes;
                };

                const keyData = base64ToBytes(this.secret);
                const messageData = encoder.encode(payload);

                const key = await crypto.subtle.importKey(
                    'raw',
                    keyData,
                    { name: 'HMAC', hash: 'SHA-256' },
                    false,
                    ['sign']
                );

                const signature = await crypto.subtle.sign('HMAC', key, messageData);
                const hashArray = Array.from(new Uint8Array(signature));
                return hashArray.map(b => b.toString(16).padStart(2, '0')).join('');
            } catch (e) {
                // Fall through to fallback
            }
        }

        // Fallback using CryptoJS
        const secretWords = CryptoJS.enc.Base64.parse(this.secret);
        const signature = CryptoJS.HmacSHA256(payload, secretWords);
        return signature.toString(CryptoJS.enc.Hex);
    }

    /**
     * Generate nonce (number used once)
     */
    generateNonce() {
        if (typeof crypto !== 'undefined' && typeof crypto.getRandomValues === 'function') {
            try {
                const array = new Uint8Array(16);
                crypto.getRandomValues(array);
                return Array.from(array, byte => byte.toString(16).padStart(2, '0')).join('');
            } catch (e) {
                // Fall through
            }
        }

        // Fallback using CryptoJS
        return CryptoJS.lib.WordArray.random(16).toString(CryptoJS.enc.Hex);
    }

    /**
     * Get current timestamp
     */
    getTimestamp() {
        return Math.floor(Date.now() / 1000);
    }

    /**
     * Get cookie value
     */
    getCookie(name) {
        if (!document.cookie) {
            return null;
        }

        const xsrfCookies = document.cookie.split(';')
            .map(c => c.trim())
            .filter(c => c.startsWith(name + '='));

        if (xsrfCookies.length === 0) {
            return null;
        }
        return decodeURIComponent(xsrfCookies[0].split('=')[1]);
    }

    /**
     * Get meta content
     */
    getMetaContent(name) {
        const meta = document.querySelector(`meta[name="${name}"]`);
        return meta ? meta.getAttribute('content') : null;
    }

    /**
     * Make secure API call
     */
    async callSecureApi(url, options = {}) {
        // Ensure initialized
        if (!this.initialized) {
            await this.init();
        }

        const method = options.method || 'POST';
        const body = options.body ? JSON.stringify(options.body) : '';
        const timestamp = this.getTimestamp();
        const nonce = this.generateNonce();

        // Extract path from URL
        const urlObj = new URL(url, window.location.origin);
        const path = urlObj.pathname;

        // Generate signature
        const signature = await this.generateSignature(method, path, timestamp, nonce, body);

        // Prepare headers
        const headers = {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Request-Signature': signature,
            'X-Request-Timestamp': timestamp.toString(),
            'X-Request-Nonce': nonce,
            'X-Browser-Fingerprint': this.fingerprint,
            'X-XSRF-TOKEN': this.getCookie('XSRF-TOKEN'),
            ...(options.headers || {})
        };

        // Make request
        const response = await fetch(url, {
            method,
            headers,
            body: method !== 'GET' ? body : undefined,
            credentials: 'same-origin'
        });

        return response;
    }

    /**
     * Wrapper for axios-style calls
     */
    async post(url, data, config = {}) {
        const response = await this.callSecureApi(url, {
            method: 'POST',
            body: data,
            headers: config.headers
        });

        if (!response.ok) {
            let errorMessage = `HTTP ${response.status}: ${response.statusText}`;
            let errorData = {};

            try {
                errorData = await response.json();
                if (errorData.response && errorData.response.message) {
                    errorMessage = errorData.response.message;
                } else if (errorData.error) {
                    errorMessage = errorData.error;
                }
            } catch (e) {
                // Ignore JSON parse error
            }

            const headers = {};
            response.headers.forEach((value, key) => {
                headers[key] = value;
            });

            const error = new Error(errorMessage);
            error.response = {
                data: errorData,
                status: response.status,
                statusText: response.statusText,
                headers: headers
            };
            throw error;
        }

        return {
            data: await response.json(),
            status: response.status,
            statusText: response.statusText,
            headers: response.headers
        };
    }

    /**
     * Get fingerprint (for debugging)
     */
    getFingerprint() {
        return this.fingerprint;
    }
}

// Create global instance
window.apiSecurity = new ApiSecurityHelper();

// Auto-initialize on page load
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        window.apiSecurity.init();
    });
} else {
    window.apiSecurity.init();
}
