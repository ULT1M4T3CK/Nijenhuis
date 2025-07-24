#!/usr/bin/env node

// Image Optimization Script for Mobile Performance
// This script helps optimize images for faster loading on mobile devices

const fs = require('fs');
const path = require('path');

function generateImageOptimizationCSS() {
    const css = `
/* Mobile Image Optimization CSS */
/* Add this to your existing CSS for better mobile image performance */

/* Responsive images */
.responsive-img {
    width: 100%;
    height: auto;
    display: block;
    max-width: 100%;
}

/* Different image sizes for different screen sizes */
@media (max-width: 480px) {
    .hero {
        background-image: url('Images/Boats/zeilboot-4-5-mobile.jpg');
    }
    
    .page-header {
        background-image: url('Images/belterwijde-mobile.jpg');
    }
}

@media (min-width: 481px) and (max-width: 768px) {
    .hero {
        background-image: url('Images/Boats/zeilboot-4-5-tablet.jpg');
    }
    
    .page-header {
        background-image: url('Images/belterwijde-tablet.jpg');
    }
}

/* WebP support with fallbacks */
@supports (background-image: url('image.webp')) {
    .hero {
        background-image: url('Images/Boats/zeilboot-4-5.webp');
    }
    
    .page-header {
        background-image: url('Images/belterwijde.webp');
    }
    
    @media (max-width: 480px) {
        .hero {
            background-image: url('Images/Boats/zeilboot-4-5-mobile.webp');
        }
        
        .page-header {
            background-image: url('Images/belterwijde-mobile.webp');
        }
    }
}

/* Lazy loading styles */
.lazy {
    opacity: 0;
    transition: opacity 0.3s;
}

.lazy.loaded {
    opacity: 1;
}

/* Progressive image loading */
.progressive-img {
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    filter: blur(5px);
    transition: filter 0.3s;
}

.progressive-img.loaded {
    filter: none;
}
`;

    fs.writeFileSync('mobile-image-optimizations.css', css.trim());
    console.log('✅ Mobile image optimization CSS generated: mobile-image-optimizations.css');
}

function generateImageHTML() {
    const html = `
<!-- Responsive Image Examples -->
<!-- Add these patterns to your HTML for optimal mobile image loading -->

<!-- Basic responsive image -->
<img src="Images/example.jpg" 
     alt="Description" 
     class="responsive-img" 
     loading="lazy"
     decoding="async">

<!-- Picture element with multiple sources for different screen sizes -->
<picture>
    <source media="(max-width: 480px)" 
            srcset="Images/example-mobile.webp" 
            type="image/webp">
    <source media="(max-width: 480px)" 
            srcset="Images/example-mobile.jpg">
    <source media="(max-width: 768px)" 
            srcset="Images/example-tablet.webp" 
            type="image/webp">
    <source media="(max-width: 768px)" 
            srcset="Images/example-tablet.jpg">
    <source srcset="Images/example.webp" 
            type="image/webp">
    <img src="Images/example.jpg" 
         alt="Description" 
         class="responsive-img"
         loading="lazy"
         decoding="async">
</picture>

<!-- Lazy loading with data-src -->
<img data-src="Images/example.jpg" 
     src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 400 300'%3E%3Crect fill='%23f0f0f0'/%3E%3C/svg%3E"
     alt="Description" 
     class="lazy responsive-img"
     loading="lazy">
`;

    fs.writeFileSync('responsive-image-examples.html', html.trim());
    console.log('✅ Responsive image examples generated: responsive-image-examples.html');
}

function generateOptimizationRecommendations() {
    const recommendations = `
# Mobile Image Optimization Recommendations

## Current Status
- Images directory size: ~17MB
- Multiple boat images need optimization

## Recommendations for Mobile Performance:

### 1. Image Compression
- Compress JPEG images to 70-80% quality
- Use WebP format for modern browsers
- Generate multiple sizes for different devices

### 2. Recommended Image Sizes
- Mobile (≤480px): 480px wide
- Tablet (481-768px): 768px wide  
- Desktop (>768px): 1200px wide

### 3. Priority Optimization List
1. Hero background: Images/Boats/zeilboot-4-5.jpg
2. Page header: Images/belterwijde.jpg
3. Boat images in Images/Boats/ directory

### 4. Implementation Steps
1. Use tools like imagemin, sharp, or online tools to compress images
2. Generate WebP versions of all images
3. Create responsive image sets
4. Implement lazy loading for below-the-fold images

### 5. Tools for Image Optimization
- Online: TinyPNG, Squoosh.app, ImageOptim
- CLI: imagemin, sharp, cwebp
- Build tools: webpack-imagemin-plugin, gulp-imagemin

### 6. Expected Performance Improvements
- 60-80% reduction in image file sizes
- Faster mobile loading times
- Better mobile network performance
- Improved Core Web Vitals scores

## Example Implementation:
\`\`\`bash
# Convert to WebP
cwebp Images/Boats/zeilboot-4-5.jpg -o Images/Boats/zeilboot-4-5.webp -q 80

# Resize for mobile
convert Images/Boats/zeilboot-4-5.jpg -resize 480x Images/Boats/zeilboot-4-5-mobile.jpg

# Compress existing images
imagemin Images/Boats/*.jpg --out-dir=Images/Boats/optimized --plugin=mozjpeg
\`\`\`
`;

    fs.writeFileSync('IMAGE_OPTIMIZATION_GUIDE.md', recommendations.trim());
    console.log('✅ Image optimization guide generated: IMAGE_OPTIMIZATION_GUIDE.md');
}

// Generate all optimization files
console.log('🚀 Generating mobile image optimization resources...\n');

generateImageOptimizationCSS();
generateImageHTML();
generateOptimizationRecommendations();

console.log('\n✨ Mobile image optimization resources generated successfully!');
console.log('\nNext steps:');
console.log('1. Review IMAGE_OPTIMIZATION_GUIDE.md for detailed recommendations');
console.log('2. Add mobile-image-optimizations.css to your build process');
console.log('3. Use responsive-image-examples.html as reference for implementation');
console.log('4. Compress and optimize images in the Images/ directory');