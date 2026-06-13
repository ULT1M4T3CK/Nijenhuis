# Full SEO & AI Search Optimization Audit — nijenhuis-botenverhuur.com

**Date:** 12 June 2026
**Audited by:** Viktor AI

---

## Overall Verdict

The site has a **strong technical foundation** — especially in structured data, AI discoverability, and security. The major gaps are in **organic keyword rankings** (not visible for most non-branded searches), **duplicate content** on the /tarieven and boat detail pages, and **missing social proof & backlink signals** on the website itself. Below is the full breakdown.

---

## 1. TECHNICAL SEO

### Security Headers — 6/7 ✅

| Header | Status | Value |
|--------|--------|-------|
| Strict-Transport-Security | ✅ | max-age=31536000; includeSubDomains; preload |
| X-Content-Type-Options | ✅ | nosniff |
| X-Frame-Options | ✅ | SAMEORIGIN |
| X-XSS-Protection | ✅ | 1; mode=block |
| Referrer-Policy | ✅ | strict-origin-when-cross-origin |
| Permissions-Policy | ✅ | camera=(), microphone=(), geolocation=() |
| Content-Security-Policy | ❌ | MISSING |

**Action:** Add a `Content-Security-Policy` header. Start with `default-src 'self'; script-src 'self' 'unsafe-inline' https://maps.googleapis.com; style-src 'self' 'unsafe-inline'; img-src 'self' data: https:; font-src 'self'; frame-src https://maps.googleapis.com https://www.google.com;` and test.

### SSL Certificate
- ✅ TLSv1.3 / TLS_AES_256_GCM_SHA384
- ✅ Let's Encrypt (auto-renewal)
- ⚠️ **Expires Jul 24, 2026** — ensure auto-renewal is working

### Redirects
- ✅ HTTP → HTTPS (301)
- ✅ www → non-www (301)

### Compression
- ✅ Gzip enabled (`content-encoding: gzip`)

### Server
- nginx, response times 300–325ms per page

---

## 2. ON-PAGE SEO

### Homepage
- ✅ Title: "Bootje huren Giethoorn & Weerribben | Nijenhuis Botenverhuur" (58 chars — good)
- ✅ Description: Rich with keywords, checkmarks, truncated at ~155 chars — good
- ✅ H1: "Bootje huren bij Giethoorn & Weerribben — vanaf €20/dag"
- ✅ Word count: 692 words
- ✅ 40 internal links
- ✅ lang="nl"
- ✅ Geo meta tags (region, placename, coordinates)

### Subpages Summary

| Page | Title OK | Desc OK | H1 OK | Words | Schema Types | Issues |
|------|----------|---------|-------|-------|-------------|--------|
| /botenverhuur | ✅ | ✅ | ✅ | 940 | BoatRental, BreadcrumbList, Service, FAQPage | — |
| /vakantiehuis | ✅ | ✅ | ✅ | 569 | BoatRental, BreadcrumbList, VacationRental | — |
| /camping | ✅ | ✅ | ✅ | 541 | BoatRental, BreadcrumbList, Campground | — |
| /giethoorn | ✅ | ✅ | ✅ | 1587 | BoatRental, Place, BreadcrumbList, FAQPage | Strongest content page |
| /belt-schutsloot | ✅ | ✅ | ✅ | 1097 | BoatRental, Place, BreadcrumbList, FAQPage | — |
| /wanneperveen | ✅ | ✅ | ✅ | 1033 | BoatRental, Place, BreadcrumbList, FAQPage | — |
| /veelgestelde-vragen | ✅ | ✅ | ✅ | 880 | BoatRental, FAQPage, BreadcrumbList | — |
| /vaarkaart | ✅ | ✅ | ✅ | 537 | BoatRental, HowTo, BreadcrumbList | — |
| /contact | ✅ | ✅ | ✅ | 346 | BoatRental, BreadcrumbList | — |
| /blog | ✅ | ✅ | ✅ | 514 | BoatRental, Blog, BreadcrumbList | — |
| /te-koop | ✅ | ✅ | ✅ | 269 | BoatRental, BreadcrumbList | Thin content (269 words) |
| /tarieven | ❌ | ❌ | ❌ | 692 | BoatRental only | **DUPLICATE of homepage** |
| /classic-tender-720 | ✅ | ✅ | ✅ | 692 | BoatRental, Product/Vehicle | Body falls back to homepage content |
| /electrosloop-10 | ✅ | ✅ | ✅ | 692 | BoatRental, Product/Vehicle | Body falls back to homepage content |

### 🚨 Critical: Duplicate Content Issues
1. **/tarieven** has the *exact same* title, H1, meta description, and body as the homepage. Google may see this as duplicate content and ignore or penalize it.
2. **Boat detail pages** (classic-tender-720, electrosloop-10, etc.) have unique H1s and titles but the page body falls back to the homepage content (same word count: 692). Each boat page should have unique descriptive content about that specific boat.

### Images — All OK
- 16 images on homepage, 0 missing alt text ✅
- All alt texts are descriptive ✅
- ❌ **No lazy loading** — all images load eagerly (~2 MB total on homepage)
- **Action:** Add `loading="lazy"` to below-the-fold images

### Heading Hierarchy
- ✅ Single H1 on most pages
- ❌ Blog post "bootje-huren-drenthe" has **2 × H1 tags** — fix to 1

---

## 3. STRUCTURED DATA — Excellent ⭐

This is one of the strongest areas of the site.

**Homepage schemas:**
- `BoatRental` + `LocalBusiness` + `TouristAttraction` (combined)
- `speakable` specification (AI-ready)
- `aggregateRating` present
- `sameAs` links (Google Maps, Facebook, TripAdvisor)
- `openingHoursSpecification`
- `geo` coordinates
- `availableLanguage: [nl, en, de]`

**Per-page schemas:**
- `BreadcrumbList` on all subpages ✅
- `FAQPage` on 5 pages ✅
- `Product` + `Vehicle` on boat detail pages ✅
- `Article` on blog posts ✅
- `Blog` on blog index ✅
- `Campground`, `VacationRental`, `HowTo`, `Place` on relevant pages ✅

**Minor gap:** Blog articles missing `article:published_time` and `article:modified_time` Open Graph meta tags. Google uses these for freshness signals.

---

## 4. AI SEARCH OPTIMIZATION — Excellent ⭐

| Feature | Status |
|---------|--------|
| /llms.txt | ✅ Present, comprehensive, with .md links |
| /llms-full.txt | ✅ Full site content dump for AI ingestion |
| /llms-nijenhuis.txt | ✅ Dedicated business profile file |
| /index.md (markdown alt) | ✅ Linked via HTTP `Link:` header |
| robots.txt AI bots | ✅ GPTBot, ChatGPT-User, OAI-SearchBot, ClaudeBot, PerplexityBot, Google-Extended, Applebot-Extended, Meta-ExternalAgent, etc. — all explicitly allowed |
| Speakable specification | ✅ cssSelector targets hero & intro paragraphs |
| Crawl-delay | ✅ 5 seconds (reasonable) |

**This is best-in-class for a local business.** Most competitors have zero AI optimization.

---

## 5. PERFORMANCE

| Metric | Value |
|--------|-------|
| First Contentful Paint | 1,060 ms |
| DOM Interactive | 1,035 ms |
| DOM Content Loaded | 1,786 ms |
| Full Load | 1,952 ms |
| Resources | 39 requests |
| Total Transfer | ~2.4 MB |
| Image Weight | 2,037 KB (85% of payload) |

**Verdict:** Decent but images are the bottleneck. The ~2 MB of images should be cut to ~500 KB with:
1. WebP/AVIF format conversion
2. Responsive `srcset` with multiple sizes
3. Lazy loading for below-fold images
4. Image compression (quality 80 is fine for photos)

---

## 6. SITEMAP & INDEXING

- ✅ sitemap.xml present with hreflang annotations per URL
- ✅ All key pages included
- ✅ `lastmod` dates present (2026-06-05)
- ✅ Priority values set appropriately
- ⚠️ Blog post URLs not individually listed in sitemap (only /blog index) — add each post for faster indexing

### Indexed Pages (via site: search)
Found: Homepage, /vakantiehuis, /blog, /botenverhuur, /contact visible in search results.

---

## 7. SEARCH RANKINGS

| Query | Position | Note |
|-------|----------|------|
| "Nijenhuis Botenverhuur" | #1 | ✅ Brand search works |
| "boot huren Wanneperveen" | Not in top 3 | ❌ botentehuur.nl, clickandboat.com dominate |
| "boot huren Giethoorn" | Not visible | ❌ giethoornvillage.com dominates |
| "sloep huren Weerribben" | Not visible | ❌ sloepverhuurzwartsluis.nl, sloepvaren.nl rank |
| "fluisterboot huren Giethoorn" | Not visible | ❌ fluisterboot.nl dominates |
| "kano huren Wanneperveen" | /contact page | ⚠️ Contact page ranks, not main content |
| "boot verhuur Overijssel" | Not visible | ❌ botentehuur.nl dominates |
| "Giethoorn boat rental" | Not visible | ❌ giethoornvillage.com dominates |

**This is the biggest opportunity.** The site has great content but lacks the ranking authority to compete for high-value non-branded keywords.

---

## 8. MISSING ELEMENTS

| Element | Status | Impact |
|---------|--------|--------|
| Social media links on site | ❌ None visible | FB/Insta/TripAdvisor links in footer would boost trust signals |
| Google Business Profile link | ❌ Not linked on site | Missing easy authority signal |
| Google Reviews widget | ⚠️ Testimonials exist but no live Google/TripAdvisor feed | Real reviews build trust + fresh content |
| Web app manifest | ❌ Missing | Minor PWA signal |
| theme-color meta tag | ❌ Missing | Minor mobile browser styling |
| Content-Security-Policy | ❌ Missing | Security + trust signal |
| Admin link in footer | ⚠️ Exposed | /pages/admin-login.php visible to crawlers; add nofollow or remove |
| Social sharing on blog posts | ❌ Not checked | Would help content distribution |

---

## 9. MOBILE USABILITY

- ✅ Viewport meta tag correct
- ✅ No horizontal overflow
- ✅ Font sizes adequate (only 1/153 elements <12px)
- ⚠️ **19 of 80 tap targets too small** (<44px) — buttons/links need larger touch areas
- ✅ Responsive layout works

---

## 10. HREFLANG & INTERNATIONAL

- ✅ 3 languages: nl (default), en, de
- ✅ hreflang tags on all pages
- ✅ x-default set to Dutch
- ✅ Sitemap includes hreflang annotations
- ✅ OG locale tags (nl_NL + alternates en_US, de_DE)
- ⚠️ Language switching via query param (`?lang=en`) — works but dedicated paths (/en/, /de/) are better for SEO

---

## TOP 10 ACTION ITEMS (Priority Order)

### 🔴 Critical
1. **Fix /tarieven duplicate content** — Either redirect to homepage, create unique pricing page content, or remove from sitemap
2. **Add unique content to boat detail pages** — Each boat needs its own 300+ word description, features, specs, and use cases instead of falling back to homepage content
3. **Build backlink authority** — Register on local directories (VVV Overijssel, Ontdek Meppel, Hiswa, ANWB Waterkaart), tourism platforms, and get listed on Giethoorn aggregator sites

### 🟡 Important
4. **Add individual blog posts to sitemap.xml** — Currently only /blog index is in sitemap; add each /blog/{slug} URL
5. **Add published/modified dates to blog articles** — `article:published_time` and `article:modified_time` OG meta tags for freshness signals
6. **Fix duplicate H1 on blog posts** — "bootje-huren-drenthe" has 2 H1 tags
7. **Implement lazy loading on images** — Add `loading="lazy"` to below-fold images; saves ~1.5 MB on initial load
8. **Add social media links to footer** — Facebook, TripAdvisor, Google Business Profile links

### 🟢 Nice to Have
9. **Add Content-Security-Policy header** — Complete the 7/7 security headers
10. **Convert images to WebP** — Reduce ~2 MB image payload to ~500 KB
11. **Remove or nofollow admin link** — `/pages/admin-login.php` is visible in footer
12. **Add theme-color + web manifest** — Minor PWA and mobile browser signals

---

## SUMMARY SCORECARD

| Category | Score | Notes |
|----------|-------|-------|
| Technical SEO | 8/10 | Strong foundation, CSP missing |
| On-Page SEO | 7/10 | Good meta + content, but duplicate content issues |
| Structured Data | 10/10 | Best-in-class for a local business |
| AI Search Optimization | 10/10 | llms.txt, speakable, AI bot allowances — excellent |
| Performance | 7/10 | Fast server, but images unoptimized |
| Content | 8/10 | 10 blog posts, good destinations pages, but boat pages thin |
| Mobile | 8/10 | Responsive, minor tap target issues |
| Search Rankings | 4/10 | Only ranks for brand name; invisible for key terms |
| Backlinks & Authority | 3/10 | Low domain authority, few external signals |
| **Overall** | **7.2/10** | |

The site is technically excellent — the bottleneck is now *authority and content depth* to rank for competitive non-branded keywords.
