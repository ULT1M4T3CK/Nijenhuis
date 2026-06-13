/**
 * Client-side image resize/re-encode for admin uploads (te koop, boat gallery).
 * Falls back to the original File on decode errors or unsupported types.
 */
(function (global) {
    var DEFAULT_MAX_EDGE = 1920;
    var DEFAULT_JPEG_QUALITY = 0.82;
    var SKIP_SIZE_BYTES = 200 * 1024;

    function loadBitmap(file) {
        if (global.createImageBitmap) {
            return createImageBitmap(file).catch(function () {
                return loadBitmapViaImage(file);
            });
        }
        return loadBitmapViaImage(file);
    }

    function loadBitmapViaImage(file) {
        return new Promise(function (resolve, reject) {
            var url = URL.createObjectURL(file);
            var img = new Image();
            img.onload = function () {
                URL.revokeObjectURL(url);
                resolve(img);
            };
            img.onerror = function () {
                URL.revokeObjectURL(url);
                reject(new Error('decode'));
            };
            img.src = url;
        });
    }

    function computeSize(sw, sh, maxEdge) {
        if (sw <= maxEdge && sh <= maxEdge) {
            return { w: sw, h: sh };
        }
        var r = Math.min(maxEdge / sw, maxEdge / sh);
        return { w: Math.round(sw * r), h: Math.round(sh * r) };
    }

    function hasTransparency(imageData) {
        var d = imageData.data;
        for (var i = 3; i < d.length; i += 16) {
            if (d[i] < 255) {
                return true;
            }
        }
        return false;
    }

    function releaseBitmap(bitmap) {
        if (bitmap && typeof bitmap.close === 'function') {
            bitmap.close();
        }
    }

    /**
     * @param {File} file
     * @param {{ maxEdge?: number, quality?: number }} [opts]
     * @returns {Promise<File>}
     */
    function compressImageForUpload(file, opts) {
        opts = opts || {};
        if (!file || !file.type || file.type.indexOf('image/') !== 0) {
            return Promise.resolve(file);
        }
        if (file.type === 'image/gif') {
            return Promise.resolve(file);
        }

        var maxEdge = opts.maxEdge != null ? opts.maxEdge : DEFAULT_MAX_EDGE;
        var quality = opts.quality != null ? opts.quality : DEFAULT_JPEG_QUALITY;

        return loadBitmap(file).then(function (bitmap) {
            var sw = bitmap.width;
            var sh = bitmap.height;

            if (file.size < SKIP_SIZE_BYTES && sw <= maxEdge && sh <= maxEdge) {
                releaseBitmap(bitmap);
                return file;
            }

            var dims = computeSize(sw, sh, maxEdge);
            var w = dims.w;
            var h = dims.h;

            var canvas = document.createElement('canvas');
            canvas.width = w;
            canvas.height = h;
            var ctx = canvas.getContext('2d');
            if (!ctx) {
                releaseBitmap(bitmap);
                return file;
            }
            ctx.drawImage(bitmap, 0, 0, w, h);
            releaseBitmap(bitmap);

            var imageData = ctx.getImageData(0, 0, w, h);
            var usePng = hasTransparency(imageData);

            var baseName = file.name ? file.name.replace(/\.[^.]+$/, '') : 'image';

            return new Promise(function (resolve) {
                function finish(blob, name, type) {
                    if (!blob) {
                        resolve(file);
                        return;
                    }
                    resolve(new File([blob], name, { type: type }));
                }
                if (usePng) {
                    canvas.toBlob(function (blob) {
                        finish(blob, baseName + '.png', 'image/png');
                    }, 'image/png');
                } else {
                    canvas.toBlob(function (blob) {
                        finish(blob, baseName + '.jpg', 'image/jpeg');
                    }, 'image/jpeg', quality);
                }
            });
        }).catch(function () {
            return file;
        });
    }

    /**
     * @param {File} file
     * @param {{ maxEdge?: number, quality?: number }} [opts]
     * @returns {Promise<string>}
     */
    function compressToDataURL(file, opts) {
        return compressImageForUpload(file, opts).then(function (out) {
            return new Promise(function (resolve, reject) {
                var reader = new FileReader();
                reader.onload = function () {
                    resolve(reader.result);
                };
                reader.onerror = function () {
                    reject(reader.error);
                };
                reader.readAsDataURL(out);
            });
        });
    }

    global.compressImageForUpload = compressImageForUpload;
    global.compressToDataURL = compressToDataURL;
})(typeof window !== 'undefined' ? window : globalThis);
