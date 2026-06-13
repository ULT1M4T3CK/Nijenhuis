#!/usr/bin/env python3
"""
Website Content Extractor
Extracts relevant content from HTML pages for chatbot training and query processing
"""

import os
import re
from typing import List, Dict, Set
from html.parser import HTMLParser
from pathlib import Path

try:
    from bs4 import BeautifulSoup
    BS4_AVAILABLE = True
except ImportError:
    BS4_AVAILABLE = False
    print("⚠️ BeautifulSoup4 not available. Install with: pip install beautifulsoup4")


class WebsiteContentExtractor:
    """Extract and structure content from HTML pages"""
    
    def __init__(self, pages_directory: str = None):
        """
        Initialize content extractor
        
        Args:
            pages_directory: Path to directory containing HTML pages
        """
        if pages_directory is None:
            # Default to pages directory relative to project root
            current_dir = os.path.dirname(os.path.abspath(__file__))
            project_root = os.path.join(current_dir, '..', '..', '..')
            pages_directory = os.path.join(project_root, 'pages')
        
        self.pages_directory = pages_directory
        self.cached_content = None
    
    def extract_all_content(self, force_refresh: bool = False) -> str:
        """
        Extract content from all HTML pages
        
        Args:
            force_refresh: Force re-extraction even if cached
            
        Returns:
            Combined content string
        """
        if self.cached_content and not force_refresh:
            return self.cached_content
        
        if not os.path.exists(self.pages_directory):
            print(f"⚠️ Pages directory not found: {self.pages_directory}")
            return ""
        
        all_content = []
        
        # Process all HTML files
        html_files = list(Path(self.pages_directory).glob("*.html"))
        
        for html_file in html_files:
            # Skip admin and offline pages
            if 'admin' in str(html_file) or 'offline' in str(html_file):
                continue
            
            try:
                content = self.extract_from_file(str(html_file))
                if content:
                    all_content.append(f"=== {html_file.stem} ===\n{content}\n")
            except Exception as e:
                print(f"⚠️ Error extracting content from {html_file}: {e}")
        
        combined_content = "\n\n".join(all_content)
        self.cached_content = combined_content
        return combined_content
    
    def extract_from_file(self, file_path: str) -> str:
        """
        Extract content from a single HTML file
        
        Args:
            file_path: Path to HTML file
            
        Returns:
            Extracted text content
        """
        try:
            with open(file_path, 'r', encoding='utf-8') as f:
                html_content = f.read()
            
            if BS4_AVAILABLE:
                return self._extract_with_bs4(html_content)
            else:
                return self._extract_with_regex(html_content)
        except Exception as e:
            print(f"⚠️ Error reading {file_path}: {e}")
            return ""
    
    def _extract_with_bs4(self, html_content: str) -> str:
        """Extract content using BeautifulSoup"""
        soup = BeautifulSoup(html_content, 'html.parser')
        
        # Remove script and style elements
        for script in soup(["script", "style", "noscript", "nav", "footer", "header"]):
            script.decompose()
        
        # Extract text from important elements
        content_parts = []
        
        # Get main content areas
        main_content = soup.find('main') or soup.find('body')
        if main_content:
            # Extract headings with context
            for heading in main_content.find_all(['h1', 'h2', 'h3', 'h4', 'h5', 'h6']):
                text = heading.get_text(strip=True)
                if text and len(text) > 3:
                    # Clean emojis and special chars
                    text = self._clean_text(text)
                    content_parts.append(f"{heading.name.upper()}: {text}")
            
            # Extract paragraphs (most important for Q&A)
            for p in main_content.find_all('p'):
                text = p.get_text(strip=True)
                text = self._clean_text(text)
                if text and len(text) > 15:  # Skip very short text
                    content_parts.append(text)
            
            # Extract pricing table data (very important)
            for table in main_content.find_all('table'):
                rows = []
                for tr in table.find_all('tr'):
                    cells = []
                    for td in tr.find_all(['td', 'th']):
                        cell_text = td.get_text(strip=True)
                        cell_text = self._clean_text(cell_text)
                        if cell_text:
                            cells.append(cell_text)
                    if cells:
                        rows.append(" | ".join(cells))
                if rows:
                    content_parts.append("PRIJZENTABEL:\n" + "\n".join(rows))
            
            # Extract list items
            for ul in main_content.find_all(['ul', 'ol']):
                items = []
                for li in ul.find_all('li', recursive=False):
                    text = li.get_text(strip=True)
                    text = self._clean_text(text)
                    if text and len(text) > 5:
                        items.append(f"- {text}")
                if items:
                    content_parts.append("\n".join(items))
            
            # Extract boat/service information from divs with specific classes
            for div in main_content.find_all('div', class_=re.compile(r'boat|pricing|service|card|feature')):
                text = div.get_text(strip=True)
                text = self._clean_text(text)
                if text and len(text) > 30:  # Only substantial content
                    # Avoid duplicating content already extracted
                    if not any(text[:50] in part for part in content_parts[-10:]):
                        content_parts.append(text)
            
            # Extract FAQ sections (common patterns)
            for faq_section in main_content.find_all(['div', 'section'], class_=re.compile(r'faq|question|answer|accordion')):
                faq_text = faq_section.get_text(strip=True)
                faq_text = self._clean_text(faq_text)
                if faq_text and len(faq_text) > 20:
                    # Format as Q&A
                    if '?' in faq_text:
                        content_parts.append(f"FAQ: {faq_text}")
            
            # Extract data attributes that might contain structured info
            for element in main_content.find_all(attrs={'data-boat': True}):
                boat_info = element.get('data-boat')
                if boat_info:
                    content_parts.append(f"BOAT_INFO: {boat_info}")
            
            # Extract structured data from data attributes
            for element in main_content.find_all(attrs={'data-price': True}):
                price = element.get('data-price')
                boat_name = element.get('data-name', '')
                if price:
                    content_parts.append(f"PRICE: {boat_name} - €{price}")
            
            # Extract spans and divs with role="text" or aria-label
            for element in main_content.find_all(['span', 'div'], attrs={'aria-label': True}):
                aria_text = element.get('aria-label')
                if aria_text and len(aria_text) > 10:
                    aria_text = self._clean_text(aria_text)
                    if aria_text not in content_parts[-20:]:  # Avoid duplicates
                        content_parts.append(aria_text)
        
        # Extract metadata
        title = soup.find('title')
        if title:
            title_text = self._clean_text(title.get_text(strip=True))
            if title_text:
                content_parts.insert(0, f"TITLE: {title_text}")
        
        meta_desc = soup.find('meta', attrs={'name': 'description'})
        if meta_desc and meta_desc.get('content'):
            desc_text = self._clean_text(meta_desc['content'])
            if desc_text:
                content_parts.insert(1, f"DESCRIPTION: {desc_text}")
        
        return "\n".join(content_parts)
    
    def _clean_text(self, text: str) -> str:
        """Clean extracted text by removing emojis and normalizing whitespace"""
        import re
        # Remove emojis (Unicode ranges for emojis)
        text = re.sub(r'[\U0001F600-\U0001F64F]', '', text)  # Emoticons
        text = re.sub(r'[\U0001F300-\U0001F5FF]', '', text)  # Misc Symbols
        text = re.sub(r'[\U0001F680-\U0001F6FF]', '', text)  # Transport
        text = re.sub(r'[\U0001F1E0-\U0001F1FF]', '', text)  # Flags
        text = re.sub(r'[\U00002702-\U000027B0]', '', text)  # Dingbats
        text = re.sub(r'[\U000024C2-\U0001F251]', '', text)  # Enclosed characters
        
        # Normalize whitespace
        text = re.sub(r'\s+', ' ', text)
        text = text.strip()
        
        return text
    
    def _extract_with_regex(self, html_content: str) -> str:
        """Fallback extraction using regex"""
        # Remove script and style tags
        html_content = re.sub(r'<script[^>]*>.*?</script>', '', html_content, flags=re.DOTALL | re.IGNORECASE)
        html_content = re.sub(r'<style[^>]*>.*?</style>', '', html_content, flags=re.DOTALL | re.IGNORECASE)
        
        # Extract title
        title_match = re.search(r'<title[^>]*>(.*?)</title>', html_content, re.IGNORECASE | re.DOTALL)
        content_parts = []
        if title_match:
            title = re.sub(r'<[^>]+>', '', title_match.group(1)).strip()
            if title:
                content_parts.append(f"TITLE: {title}")
        
        # Extract meta description
        desc_match = re.search(r'<meta[^>]*name=["\']description["\'][^>]*content=["\']([^"\']+)["\']', html_content, re.IGNORECASE)
        if desc_match:
            content_parts.append(f"DESCRIPTION: {desc_match.group(1)}")
        
        # Extract headings
        for heading_match in re.finditer(r'<h([1-6])[^>]*>(.*?)</h\1>', html_content, re.IGNORECASE | re.DOTALL):
            heading_text = re.sub(r'<[^>]+>', '', heading_match.group(2)).strip()
            if heading_text:
                level = heading_match.group(1)
                content_parts.append(f"H{level}: {heading_text}")
        
        # Extract paragraphs
        for p_match in re.finditer(r'<p[^>]*>(.*?)</p>', html_content, re.IGNORECASE | re.DOTALL):
            para_text = re.sub(r'<[^>]+>', '', p_match.group(1)).strip()
            if para_text and len(para_text) > 10:
                content_parts.append(para_text)
        
        # Extract list items
        for li_match in re.finditer(r'<li[^>]*>(.*?)</li>', html_content, re.IGNORECASE | re.DOTALL):
            li_text = re.sub(r'<[^>]+>', '', li_match.group(1)).strip()
            if li_text:
                content_parts.append(f"- {li_text}")
        
        return "\n".join(content_parts)
    
    def get_structured_content(self) -> Dict[str, any]:
        """
        Get structured content organized by page and topic
        
        Returns:
            Dictionary with structured content
        """
        content = self.extract_all_content()
        
        structured = {
            'services': [],
            'prices': [],
            'contact': {},
            'opening_hours': '',
            'faq': [],
            'boats': [],
            'full_text': content
        }
        
        # Extract prices
        price_pattern = r'€\s*(\d+)|(\d+)\s*€|prijs[:\s]+(?:€\s*)?(\d+)'
        prices = re.findall(price_pattern, content, re.IGNORECASE)
        structured['prices'] = [p[0] or p[1] or p[2] for p in prices if any(p)]
        
        # Extract contact info
        phone_pattern = r'(\+?\d{1,3}[-.\s]?\(?\d{1,4}\)?[-.\s]?\d{1,4}[-.\s]?\d{1,9})'
        phones = re.findall(phone_pattern, content)
        if phones:
            structured['contact']['phone'] = phones[0]
        
        email_pattern = r'([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,})'
        emails = re.findall(email_pattern, content)
        if emails:
            structured['contact']['email'] = emails[0]
        
        # Extract opening hours
        hours_pattern = r'(?:opening|open|uur|uren)[:\s]+([^\n]+)'
        hours_match = re.search(hours_pattern, content, re.IGNORECASE)
        if hours_match:
            structured['opening_hours'] = hours_match.group(1).strip()
        
        # Extract boat types
        boat_pattern = r'(tender|electrosloep|zeilboot|kano|kajak|sup|boot)\s*(\d+)?'
        boats = re.findall(boat_pattern, content, re.IGNORECASE)
        structured['boats'] = [f"{b[0]} {b[1]}" if b[1] else b[0] for b in boats]
        
        return structured


def get_website_content(pages_directory: str = None) -> str:
    """
    Convenience function to get website content
    
    Args:
        pages_directory: Optional path to pages directory
        
    Returns:
        Combined content string
    """
    extractor = WebsiteContentExtractor(pages_directory)
    return extractor.extract_all_content()

