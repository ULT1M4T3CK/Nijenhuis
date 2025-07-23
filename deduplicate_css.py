import re
from collections import OrderedDict

def parse_css_blocks(css):
    # This regex matches selectors and their blocks, including multi-line selectors
    pattern = re.compile(r'([^{]+)\{([^}]*)\}', re.MULTILINE)
    return pattern.findall(css)

def merge_blocks(blocks):
    selector_map = OrderedDict()
    for selector, body in blocks:
        selector = selector.strip()
        body = body.strip()
        if selector not in selector_map:
            selector_map[selector] = OrderedDict()
        # Parse properties
        for line in body.split(';'):
            if ':' in line:
                prop, val = line.split(':', 1)
                selector_map[selector][prop.strip()] = val.strip()
    return selector_map

def write_css(selector_map):
    output = []
    for selector, props in selector_map.items():
        output.append(f"{selector} {{")
        for prop, val in props.items():
            output.append(f"    {prop}: {val};")
        output.append("}\n")
    return '\n'.join(output)

def deduplicate_css_file(input_path, output_path):
    with open(input_path, 'r', encoding='utf-8') as f:
        css = f.read()
    blocks = parse_css_blocks(css)
    selector_map = merge_blocks(blocks)
    deduped_css = write_css(selector_map)
    with open(output_path, 'w', encoding='utf-8') as f:
        f.write(deduped_css)
    print(f"Deduplicated CSS written to {output_path}")

# Usage:
# deduplicate_css_file('styles.css', 'styles.deduped.css')

if __name__ == "__main__":
    import sys
    if len(sys.argv) != 3:
        print("Usage: python deduplicate_css.py input.css output.css")
    else:
        deduplicate_css_file(sys.argv[1], sys.argv[2]) 