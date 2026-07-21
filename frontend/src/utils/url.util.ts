export function path(...segments: Array<string | number | null | undefined>) {
    const filtered = segments.filter((segment) => segment !== null && segment !== undefined && segment !== '').map((segment) => String(segment));

    if (filtered.length === 0) return '';

    return filtered.reduce((result, segment, index) => {
        if (index === 0) {
            return segment.replace(/\/+$/, '');
        }

        return `${result}/${segment.replace(/^\/+|\/+$/g, '')}`;
    }, '');
}
