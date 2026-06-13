# Cache Invalidation Guide

## Problem: Some Devices Show Old Website

When you deploy new content, some devices may continue showing the old website due to multiple caching layers:

1. **Service Worker Cache** - Browsers cache content for offline use
2. **Browser Cache** - Browsers cache static files (CSS/JS) for performance
3. **CDN/Proxy Cache** - If using a CDN, it may cache content

## Solution: Update Cache Version on Deployment

### Quick Fix (When Deploying New Content)

1. **Update Service Worker Cache Version**
   - Open `/frontend/public/sw.js`
   - Find the line: `const CACHE_VERSION = 'v3';`
   - Increment the version number: `'v3'` → `'v4'` → `'v5'`, etc.
   - This forces all devices to clear old cache and fetch fresh content

2. **Deploy the Changes**
   - The service worker will automatically:
     - Delete old caches
     - Install new cache with updated version
     - Force clients to update immediately

### How It Works

- **Network-First Strategy**: HTML, CSS, and JS files now use "network first" instead of "cache first"
  - This ensures users always get fresh content when available
  - Falls back to cache only if network fails (offline mode)

- **Cache Versioning**: Each deployment increments the cache version
  - Old caches are automatically deleted
  - New caches are created with the new version
  - All clients are forced to update

- **Reduced Cache Times**: 
  - CSS/JS: Reduced from 1 week to 1 hour
  - HTML/PHP: No cache (always fresh)

### Manual Cache Clear (For Testing)

If you need to manually clear cache on a device:

1. **Chrome/Edge**: 
   - Open DevTools (F12)
   - Right-click refresh button → "Empty Cache and Hard Reload"
   - Or: Application tab → Storage → Clear site data

2. **Firefox**:
   - Ctrl+Shift+Delete → Clear cache
   - Or: DevTools → Storage → Clear All

3. **Safari**:
   - Develop menu → Empty Caches
   - Or: Preferences → Advanced → Show Develop menu

4. **Mobile**:
   - Clear browser cache in settings
   - Or: Use private/incognito mode

### Automated Cache Version Update (Future Enhancement)

You could automate this by:
- Adding a build step that increments the version automatically
- Using a timestamp or git commit hash as the version
- Adding it to your deployment script

Example (add to deployment script):
```bash
# Auto-increment cache version
CURRENT_VERSION=$(grep -o "const CACHE_VERSION = '[^']*'" frontend/public/sw.js | cut -d"'" -f2)
NEW_VERSION="v$(( ${CURRENT_VERSION#v} + 1 ))"
sed -i "s/const CACHE_VERSION = '[^']*'/const CACHE_VERSION = '$NEW_VERSION'/" frontend/public/sw.js
```

## Current Status

- ✅ Service Worker: Network-first for HTML/CSS/JS
- ✅ Cache Version: v3 (increment on each deployment)
- ✅ Browser Cache: Reduced to 1 hour for CSS/JS
- ✅ HTML/PHP: No cache (always fresh)

## Troubleshooting

**Issue**: Some users still see old content after deployment

**Solutions**:
1. Verify cache version was incremented in `sw.js`
2. Check that deployment included the updated `sw.js` file
3. Wait a few minutes - service worker updates are asynchronous
4. Users can manually clear cache (see above)

**Issue**: Website loads slowly after clearing cache

**Normal**: First load after cache clear will be slower as everything is re-downloaded. Subsequent loads will be fast due to caching.
