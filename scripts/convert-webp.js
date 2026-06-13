#!/usr/bin/env node
/**
 * Image optimisation pipeline (M8 + M9)
 *
 * For every .jpg/.jpeg/.png under frontend/Images/:
 *   1. Generate a same-size .webp at quality 80
 *   2. Generate resized variants at 400w, 800w, 1200w, 1920w
 *      in both the original format and .webp
 *   3. Write a manifest.json mapping originals to their variants
 *
 * Usage:  node scripts/convert-webp.js [--dry-run]
 */
const fs = require('fs');
const path = require('path');
const sharp = require('sharp');

const ROOT = path.resolve(__dirname, '..');
const IMG_DIR = path.join(ROOT, 'frontend', 'Images');
const MANIFEST_PATH = path.join(IMG_DIR, 'manifest.json');
const WIDTHS = [400, 800, 1200, 1920];
const WEBP_QUALITY = 80;
const JPEG_QUALITY = 82;
const DRY_RUN = process.argv.includes('--dry-run');

const EXTENSIONS = new Set(['.jpg', '.jpeg', '.png']);

function walk(dir) {
  let results = [];
  for (const entry of fs.readdirSync(dir, { withFileTypes: true })) {
    const full = path.join(dir, entry.name);
    if (entry.isDirectory()) {
      results = results.concat(walk(full));
    } else if (EXTENSIONS.has(path.extname(entry.name).toLowerCase())) {
      results.push(full);
    }
  }
  return results;
}

async function processImage(filePath) {
  const rel = path.relative(IMG_DIR, filePath);
  const dir = path.dirname(filePath);
  const ext = path.extname(filePath).toLowerCase();
  const base = path.basename(filePath, ext);
  const entry = { original: rel, webp: null, variants: [] };

  const image = sharp(filePath);
  const metadata = await image.metadata();
  const origWidth = metadata.width || 0;

  const webpFull = path.join(dir, base + '.webp');
  if (!fs.existsSync(webpFull)) {
    if (!DRY_RUN) {
      await sharp(filePath)
        .webp({ quality: WEBP_QUALITY })
        .toFile(webpFull);
    }
  }
  entry.webp = path.relative(IMG_DIR, webpFull);

  for (const w of WIDTHS) {
    if (w >= origWidth) continue;

    const suffix = `-${w}w`;

    const resizedOrigPath = path.join(dir, `${base}${suffix}${ext}`);
    if (!fs.existsSync(resizedOrigPath)) {
      if (!DRY_RUN) {
        const pipeline = sharp(filePath).resize(w);
        if (ext === '.png') {
          await pipeline.png().toFile(resizedOrigPath);
        } else {
          await pipeline.jpeg({ quality: JPEG_QUALITY }).toFile(resizedOrigPath);
        }
      }
    }

    const resizedWebpPath = path.join(dir, `${base}${suffix}.webp`);
    if (!fs.existsSync(resizedWebpPath)) {
      if (!DRY_RUN) {
        await sharp(filePath)
          .resize(w)
          .webp({ quality: WEBP_QUALITY })
          .toFile(resizedWebpPath);
      }
    }

    entry.variants.push({
      width: w,
      original: path.relative(IMG_DIR, resizedOrigPath),
      webp: path.relative(IMG_DIR, resizedWebpPath),
    });
  }

  return entry;
}

async function main() {
  const files = walk(IMG_DIR);
  console.log(`Found ${files.length} images in ${IMG_DIR}`);
  if (DRY_RUN) console.log('(dry run — no files will be written)\n');

  const manifest = {};
  let created = 0;

  for (const f of files) {
    const rel = path.relative(IMG_DIR, f);
    // Skip already-generated variants (contain -400w, -800w, etc.)
    if (/-\d+w\.\w+$/.test(rel)) continue;

    try {
      const entry = await processImage(f);
      manifest[rel] = entry;
      const variantCount = 1 + entry.variants.length * 2;
      created += variantCount;
      console.log(`  ${rel}  →  ${variantCount} variants`);
    } catch (err) {
      console.error(`  ERROR ${rel}: ${err.message}`);
    }
  }

  if (!DRY_RUN) {
    fs.writeFileSync(MANIFEST_PATH, JSON.stringify(manifest, null, 2));
    console.log(`\nManifest written to ${MANIFEST_PATH}`);
  }

  console.log(`\nTotal: ${Object.keys(manifest).length} originals, ${created} generated files`);
}

main().catch(err => { console.error(err); process.exit(1); });
