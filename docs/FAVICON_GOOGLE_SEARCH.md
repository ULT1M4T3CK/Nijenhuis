# Favicon in Google Search Results

This document explains the favicon setup for nijenhuis-botenverhuur.com and how to ensure your logo shows in Google search results.

## Current Setup

- **favicon.svg** – Square 48×48 favicon with brand blue (#0071BB) background and white sail icon
- Served from `/frontend/Images/favicon.svg`
- Used in `<link rel="icon">`, Schema.org `Organization.logo`, and apple-touch-icon

## Google's Requirements

1. **Square (1:1 aspect ratio)** – fulfilled by favicon.svg
2. **Minimum 48×48 pixels** – fulfilled
3. **Visible on light backgrounds** – blue background ensures visibility in search results
4. **Crawlable** – not blocked by robots.txt
5. **Stable URL** – avoid changing the favicon URL frequently

## Optional: Add favicon.ico for Maximum Compatibility

Some crawlers fetch `https://yoursite.com/favicon.ico` first. To add:

1. Convert `frontend/Images/favicon.svg` to ICO (48×48) using:
   - [favicon.io](https://favicon.io)
   - [realfavicongenerator.net](https://realfavicongenerator.net)
2. Save as `favicon.ico` in the project root
3. Add to `components/head.php` before the SVG link:
   ```html
   <link rel="icon" type="image/x-icon" href="/favicon.ico">
   ```

## Timeline

Google may take several days to several weeks to recrawl and update the favicon in search results. Request indexing via [Google Search Console](https://search.google.com/search-console) URL Inspection tool to speed this up.
