# Compression Configuration Guide

This document explains how compression (GZip and Brotli) is configured for the Nijenhuis Botenverhuur website.

## Current Configuration

### GZip Compression ✅
GZip compression is **enabled** by default in both nginx configurations:
- `deploy/aws/nginx-aws.conf`
- `deploy/nginx/site.conf`

GZip compresses:
- HTML files (`text/html`)
- CSS files (`text/css`)
- JavaScript files (`text/javascript`, `application/javascript`)
- JSON files (`application/json`)
- XML files (`text/xml`, `application/xml`)
- SVG images (`image/svg+xml`)
- Fonts (`font/opentype`, `application/x-font-ttf`)

**Compression level:** 6 (good balance between compression ratio and CPU usage)

### Brotli Compression (Optional)
Brotli compression is **commented out** by default. Brotli provides better compression than GZip (typically 15-20% better), but requires the nginx Brotli module to be installed.

## Enabling Brotli Compression

### Step 1: Install nginx Brotli Module

**On Ubuntu/Debian:**
```bash
sudo apt-get update
sudo apt-get install nginx-module-brotli
```

**On Amazon Linux 2 / CentOS:**
```bash
# Brotli module may need to be compiled from source or installed via EPEL
# Check: https://github.com/google/ngx_brotli
```

### Step 2: Load the Module

Add to `/etc/nginx/nginx.conf` (in the `http` block, before server blocks):
```nginx
load_module modules/ngx_http_brotli_filter_module.so;
load_module modules/ngx_http_brotli_static_module.so;
```

### Step 3: Enable Brotli in Configuration

Uncomment the Brotli lines in your nginx config file:
- `deploy/aws/nginx-aws.conf` (lines ~48-52)
- `deploy/nginx/site.conf` (lines ~48-52)

Change from:
```nginx
# brotli on;
# brotli_comp_level 6;
# ...
```

To:
```nginx
brotli on;
brotli_comp_level 6;
brotli_types text/html text/plain text/css ...;
brotli_min_length 1000;
```

### Step 4: Test and Reload

```bash
# Test nginx configuration
sudo nginx -t

# Reload nginx
sudo systemctl reload nginx
```

## Testing Compression

### Using curl

**Test GZip:**
```bash
curl -H "Accept-Encoding: gzip" -I https://nijenhuis-botenverhuur.com/
```

Look for: `Content-Encoding: gzip`

**Test Brotli:**
```bash
curl -H "Accept-Encoding: br" -I https://nijenhuis-botenverhuur.com/
```

Look for: `Content-Encoding: br`

### Using Browser DevTools

1. Open Chrome/Firefox DevTools (F12)
2. Go to Network tab
3. Reload the page
4. Click on any HTML/CSS/JS file
5. Check the Response Headers:
   - `Content-Encoding: gzip` or `Content-Encoding: br`
   - `Content-Length` (compressed size)
   - Compare with original file size

### Online Tools

- **GTmetrix**: https://gtmetrix.com/
- **Google PageSpeed Insights**: https://pagespeed.web.dev/
- **WebPageTest**: https://www.webpagetest.org/

These tools will show compression status and savings.

## Expected Results

With GZip enabled, you should see:
- **HTML files**: 60-80% size reduction
- **CSS files**: 70-85% size reduction
- **JavaScript files**: 70-85% size reduction
- **JSON files**: 70-85% size reduction

With Brotli enabled, expect:
- **Additional 15-20%** compression improvement over GZip

## Apache Configuration (.htaccess)

For Apache servers, compression is configured in `.htaccess` using `mod_deflate`.

The configuration includes:
- HTML compression
- CSS/JS compression
- JSON/XML compression
- Font compression
- Compression level: 6

## Troubleshooting

### Compression Not Working

1. **Check nginx error logs:**
   ```bash
   sudo tail -f /var/log/nginx/error.log
   ```

2. **Verify gzip is enabled:**
   ```bash
   sudo nginx -T | grep gzip
   ```

3. **Check if files are too small:**
   - Files smaller than `gzip_min_length` (1000 bytes) won't be compressed
   - This is intentional to avoid overhead on tiny files

4. **Verify client supports compression:**
   - Modern browsers support GZip automatically
   - Check browser's `Accept-Encoding` header

### Brotli Not Working

1. **Verify module is loaded:**
   ```bash
   sudo nginx -T 2>&1 | grep brotli
   ```

2. **Check for errors:**
   ```bash
   sudo nginx -t
   ```

3. **Verify module installation:**
   ```bash
   ls -la /usr/lib/nginx/modules/ | grep brotli
   ```

## Performance Impact

- **CPU Usage**: Minimal (compression level 6 is a good balance)
- **Memory Usage**: Negligible
- **Bandwidth Savings**: 60-85% reduction in text-based content
- **Page Load Time**: Typically 20-40% faster for text-heavy pages

## Best Practices

1. ✅ **Compress text-based content** (HTML, CSS, JS, JSON, XML)
2. ❌ **Don't compress already compressed files** (images, videos, ZIP files)
3. ✅ **Use compression level 6** (good balance)
4. ✅ **Set minimum file size** (1000 bytes) to avoid overhead
5. ✅ **Enable Vary header** (`gzip_vary on`) for proper caching

## References

- [Nginx GZip Module Documentation](http://nginx.org/en/docs/http/ngx_http_gzip_module.html)
- [Nginx Brotli Module (GitHub)](https://github.com/google/ngx_brotli)
- [Mozilla Compression Guide](https://developer.mozilla.org/en-US/docs/Web/HTTP/Compression)
